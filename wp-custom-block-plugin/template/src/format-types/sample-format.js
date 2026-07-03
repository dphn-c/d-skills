/**
 * Sample format type: highlight text with <mark>.
 *
 * To add more formats:
 * 1. Create a new file in src/format-types/ (e.g. highlight-format.js)
 * 2. Call registerFormatType() in that file
 * 3. Import it in src/format-types/index.js
 * 4. Run npm run build
 */
import { BlockFormatControls } from '@wordpress/block-editor';
import { registerFormatType, toggleFormat } from '@wordpress/rich-text';
import { ToolbarButton } from '@wordpress/components';

const FORMAT_NAME = 'my-blocks/sample-format';

registerFormatType(FORMAT_NAME, {
	title: 'Highlight',
	tagName: 'mark',
	className: 'c-highlight',
	edit: ({ isActive, value, onChange }) => {
		const onToggle = () =>
			onChange(toggleFormat(value, { type: FORMAT_NAME }));

		return (
			<BlockFormatControls>
				<ToolbarButton
					icon="editor-textcolor"
					title="Highlight"
					isActive={isActive}
					onClick={onToggle}
				/>
			</BlockFormatControls>
		);
	},
});
