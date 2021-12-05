exports.handler = async (event) => {
  console.log(`event >`, JSON.stringify(event, null, 2));
  const {
    authorizationToken,
    requestContext: { apiId, accountId },
  } = event;

  // TODO: authorizationTokenを基に、JWTか直接Laravelへ認証手続をし、成功したらTrue
  const isAuthorized = true;
  // TODO: ユーザー情報を取得する
  const user = {
    user_code: '111111',
    email: '111111@example.com',
  };

  const response = {
    // ここがfalseの場合は認証失敗になる
    isAuthorized,
    // 後続処理に渡せる情報。vtlやリゾルバーに渡す値として利用できる
    // ここで権限チェック系の共通処理の結果を渡してあげるとよさそう
    resolverContext: {
      usercode: user.user_code,
      info: 'contextual information A',
      more_info: 'contextual information B',
    },
    // 拒否するfieldを選択できる
    deniedFields: [
      // 特定のtypeのフィールドのみを禁止したい場合
      `arn:aws:appsync:${process.env.AWS_REGION}:${accountId}:apis/${apiId}/types/Post/fields/comments`,
      // 特定のmutationを禁止したい場合
      `Mutation.createEvent`,
    ],
    ttlOverride: 10,
  };
  console.log(`response >`, JSON.stringify(response, null, 2));
  return response;
};
