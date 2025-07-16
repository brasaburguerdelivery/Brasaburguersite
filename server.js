require('dotenv').config();
const express = require('express');
const cors = require('cors');
const stripe = require('stripe')(process.env.sk_test_51RlEfzKobE3Kw4noTD63xxTMDzpCWH9oqINcDbLP6fzUPeES5kBr2leKU0s3Bwk3FV4R52G3Z0z9deU8hmtbqDPh00uhZeZ1BD);

const app = express();

// Configurações básicas
app.use(cors());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Rota de saúde da API
app.get('/api/health', (req, res) => {
  res.status(200).json({ status: 'API funcionando' });
});

// Rota para criar intenção de pagamento
app.post('/api/create-payment-intent', async (req, res) => {
  try {
    const { amount, currency = 'brl', metadata = {} } = req.body;

    // Validar o valor
    if (!amount || isNaN(amount) || amount <= 0) {
      return res.status(400).json({ error: 'Valor inválido' });
    }

    // Criar PaymentIntent no Stripe
    const paymentIntent = await stripe.paymentIntents.create({
      amount: Math.round(amount * 100), // Stripe usa centavos
      currency,
      metadata,
      automatic_payment_methods: {
        enabled: true,
      },
    });

    res.status(200).json({
      clientSecret: paymentIntent.client_secret,
      paymentIntentId: paymentIntent.id,
      amount: paymentIntent.amount,
      currency: paymentIntent.currency,
    });
  } catch (error) {
    console.error('Erro ao criar PaymentIntent:', error);
    res.status(500).json({ error: 'Erro ao processar pagamento' });
  }
});

// Rota para verificar status do pagamento
app.get('/api/payment-status/:paymentIntentId', async (req, res) => {
  try {
    const { paymentIntentId } = req.params;

    const paymentIntent = await stripe.paymentIntents.retrieve(paymentIntentId);

    res.status(200).json({
      status: paymentIntent.status,
      amount: paymentIntent.amount,
      currency: paymentIntent.currency,
      payment_method: paymentIntent.payment_method,
      created: paymentIntent.created,
    });
  } catch (error) {
    console.error('Erro ao verificar status:', error);
    res.status(500).json({ error: 'Erro ao verificar status do pagamento' });
  }
});

// Iniciar servidor
const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log(`Servidor rodando na porta ${PORT}`);
});