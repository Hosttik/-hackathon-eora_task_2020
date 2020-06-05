const path = require('path');
const webpack = require('webpack');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const VueLoaderPlugin = require('vue-loader/lib/plugin');

const projectRootDir = path.resolve(__dirname, '..');

const baseConfig = {
  mode: 'development',

  entry: './src/main.js',

  output: {
    path: path.resolve(projectRootDir, 'dist'),
    filename: 'bundle.js',
    chunkFilename: '[name].bundle.js',
    publicPath: '/'
  },

  module: {
    rules: [{
      test: /\.js$/,
      loader: 'babel-loader',
      include: [path.resolve(projectRootDir, 'src')]
    }, {
      test: /\.vue$/,
      loader: 'vue-loader',
      options: {
        loaders: {
          scss: ['vue-style-loader', 'css-loader', 'sass-loader'],
          css: ['vue-style-loader', 'css-loader', 'sass-loader']
        }
        // other vue-loader options go here
      }
    }, {
      test: /\.s(c|a)ss$/,
      use: ['vue-style-loader', 'css-loader', {
        loader: 'sass-loader', // Requires sass-loader@^7.0.0
        options: {
          implementation: require('sass'),
          fiber: require('fibers'),
          indentedSyntax: true // optional
        }, // Requires sass-loader@^8.0.0
      },],
    }, {
      test: /\.s[ac]ss$/,
      use: ['vue-style-loader', 'css-loader', 'sass-loader']
    }, {
      test: /\.css$/,

      use: ['vue-style-loader', 'css-loader', 'sass-loader']
    }, {
      test: /\.(woff(2)?|ttf|eot|svg)(\?v=\d+\.\d+\.\d+)?$/,
      use: [{
        loader: 'file-loader',
        options: {
          name: '[name].[ext]',
          outputPath: 'fonts/'
        }
      }]
    }]
  },

  resolve: {
    alias: {
      vue$: 'vue/dist/vue.esm.js',
      src: path.resolve(projectRootDir, 'src')
    },
    extensions: ['*', '.js', '.vue', '.json']
  },

  plugins: [new VueLoaderPlugin(), new HtmlWebpackPlugin({template: 'index.html'}), new webpack.ProvidePlugin({
    axios: 'axios'
  })]
};

module.exports = {
  baseConfig,
  projectRootDir
};
