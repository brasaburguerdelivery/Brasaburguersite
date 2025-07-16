OkHttpClient client = new OkHttpClient().newBuilder()
  .build();
MediaType mediaType = MediaType.parse("application/json");
RequestBody body = RequestBody.create(mediaType, "{
  \"value\": 51,
  \"webhook_url\": \"https://brasaburguerdelivery.github.io/Brasaburguer/\",
  \"split_rules\": []
}");
Request request = new Request.Builder()
  .url("https://api.pushinpay.com.br/api/pix/cashIn")
  .method("POST", body)
  .addHeader("Authorization", "Bearer")
  .addHeader("Accept", "application/json")
  .addHeader("Content-Type", "application/json")
  .build();
Response response = client.newCall(request).execute();