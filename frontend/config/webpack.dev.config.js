const path = require("path");
const webpack = require("webpack");
const merge = require("webpack-merge");

const { baseConfig, projectRootDir } = require("./webpack.base.config.js");

const defaultPort = 8080;
const port = process.env.PORT || defaultPort;

const devConfig = merge.smart(baseConfig, {
  output: {
    path: projectRootDir
  },
  module: {
    rules: [
      {
        test: /\.(jpe?g|png|gif)$/i,
        loader: "file-loader",
        options: {
          name: "[name].[ext]"
        }
      }
    ]
  },

  plugins: [new webpack.NamedModulesPlugin()],

  devServer: {
    contentBase: [
      path.join(projectRootDir, "dist"),
      path.join(projectRootDir, "src",'assets')
    ],
    port,
    historyApiFallback: true,
    inline: true,
    host: "0.0.0.0"
  },

  devtool: false,
  optimization: {},
  mode: "development"
});

module.exports = devConfig;
