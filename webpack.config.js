const path = require("path")
const HtmlWebpackPlugin = require('html-webpack-plugin');

module.exports = {
    entry: "./src/index.js",
    output: {
        path: path.join(__dirname, "/PaymentSystem"),
        filename: "index_bundle.js"
    },
    module: {
        rules: [
            {
                test: /\.(m?js|jsx)$/,
                exclude: /(node_modules|bower_components)/,
                use: {
                    loader: 'babel-loader',
                    options: {
                    presets: ['@babel/env', '@babel/react']
                    }
                }
            },
            {
                test:/\.css$/,
                loader: ["style-loader", "css-loader"]

            }
        ]
    },
    plugins: [
        new HtmlWebpackPlugin({
            template: "./src/index.html"
        })
    ]
}