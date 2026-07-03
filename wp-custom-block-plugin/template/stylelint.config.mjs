/** @type {import('stylelint').Config} */
export default {
	extends: ['stylelint-config-standard-scss', 'stylelint-config-recess-order'],
	ignoreFiles: ['**/node_modules/**'],
	rules: {
		'no-empty-source': null,
		'property-no-vendor-prefix': null,
		'comment-empty-line-before': null,
		'media-feature-range-notation': 'context',
		'at-rule-empty-line-before': [
			'always',
			{
				except: ['blockless-after-same-name-blockless', 'first-nested'],
				ignore: ['inside-block', 'after-comment'],
			},
		],
		'selector-not-notation': null,
		'selector-class-pattern': [
			'^[a-z0-9]+(-[a-z0-9]+)*(__[a-z0-9]+(-[a-z0-9]+)*)?(--[a-z0-9]+(-[a-z0-9]+)*)?$',
			{
				resolveNestedSelectors: true,
			},
		],
	},
};
