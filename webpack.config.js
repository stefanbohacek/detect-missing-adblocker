const path = require( 'path' );

module.exports = {
  mode: 'production',
  entry: {
    dfp: './src/scripts/dfp.js',
    detect: './src/scripts/detect.js',
    ads: './src/scripts/ads.js',
    styles: './src/styles/styles.scss'
  },
  output: {
    path: path.resolve( __dirname, 'dist/js' ),
    filename: '[name].min.js'
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        use: [
          {
            loader: 'babel-loader',
            options: {
              presets: ['@babel/preset-env']
            }
          }
        ]
      },
      {
        test: /\.scss$/,
        use: [
          {
            loader: 'file-loader',
            options: {
              name: '../css/[name].min.css',
              sassOptions: {
                style: 'compressed',
                // indentWidth: 4,
                includePaths: ['src/styles']
              }
            }
          },
          {
            loader: 'extract-loader'
          },
          {
            loader: 'css-loader?-url'
          },
          {
            loader: 'sass-loader'
          }          
        ]
      }
    ]
  },
};
