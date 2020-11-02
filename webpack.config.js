const path = require('path');
const { DefinePlugin, HotModuleReplacementPlugin } = require('webpack');

module.exports = {
  context: __dirname,
  mode: process.env.NODE_ENV || 'development',
  entry: './client/index.js',
  output: {
    path: path.join(__dirname, 'public'),
    filename: 'bundle.js',
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: `${__dirname}/node_modules`,
        use: {
          loader: 'babel-loader',
        },
      },
      {
        test: /\.css$/,
        use: ['style-loader', 'css-loader']
      }
    ],
  },
  plugins: [
    new DefinePlugin({
      'process.env.NODE_ENV': JSON.stringify(process.env.NODE_ENV),
    }),
    new HotModuleReplacementPlugin({
      multiStep: true,
    }),
  ],
};
