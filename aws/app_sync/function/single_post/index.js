exports.handler = async (event) => {
  console.log('event', JSON.stringify(event));

  return {
    id: 1,
    title: 'lambda auth test',
    comments: 'hello',
  };
};
