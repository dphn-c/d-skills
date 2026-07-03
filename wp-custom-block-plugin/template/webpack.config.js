const path = require('path');
const defaultConfig = require('@wordpress/scripts/config/webpack.config.js');

// RTL CSS（アラビア語等の右書き言語用）は不要なため除外
const RtlCssPlugin = require('@wordpress/scripts/plugins/rtlcss-webpack-plugin');
const plugins = defaultConfig.plugins.filter(
	(plugin) => !(plugin instanceof RtlCssPlugin)
);

module.exports = {
	...defaultConfig,
	devtool: false,
	plugins,
	entry: () => ({
		...(typeof defaultConfig.entry === 'function'
			? defaultConfig.entry()
			: defaultConfig.entry),
		'format-types': path.resolve(
			process.cwd(),
			'src/format-types/index.js'
		),
	}),
};
