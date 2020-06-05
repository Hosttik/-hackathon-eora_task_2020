const fs = require('fs');
const path = require('path');
const webpack = require('webpack');
const merge = require('webpack-merge');
const {baseConfig, projectRootDir} = require('./webpack.base.config.js');

console.log(projectRootDir);

const webpackProdConfig = merge(baseConfig, {
  devtool: false,
  output: {
    path: path.resolve(projectRootDir, '../backend/backend/web/dist'),
    publicPath: './'
  },
  module: {
    rules: [
      {
        test: /\.(jpe?g|png|gif)$/i,
        loader: "file-loader",
        options: {
          name: "./dist/[name].[ext]"
        }
      }
    ]
  },

  plugins: [new webpack.NamedModulesPlugin()],
});

module.exports = webpackProdConfig;
