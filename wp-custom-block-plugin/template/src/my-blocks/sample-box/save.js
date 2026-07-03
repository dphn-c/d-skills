import { useBlockProps } from '@wordpress/block-editor';

export default function save() {
	const blockProps = useBlockProps.save({
		className: 'wp-block-my-blocks-sample-box',
	});

	return (
		<div {...blockProps}>
			<p>Sample Box — Saved content</p>
		</div>
	);
}
