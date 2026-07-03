import { useBlockProps } from '@wordpress/block-editor';

import './editor.scss';

export default function Edit() {
	const blockProps = useBlockProps({
		className: 'wp-block-my-blocks-sample-box',
	});

	return (
		<div {...blockProps}>
			<p>Sample Box — Edit view</p>
		</div>
	);
}
