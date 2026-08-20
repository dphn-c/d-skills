/**
 * Editor preview for PHP-only dynamic blocks (theme-blocks/render).
 * Names come from Blocks.php → window.myThemeEditor.names
 */
(function (blocks, element, blockEditor, serverSideRender) {
  const el = element.createElement;
  const { registerBlockType } = blocks;
  const { useBlockProps } = blockEditor;
  const ServerSideRender = serverSideRender.default || serverSideRender;
  const names = (window.myThemeEditor && window.myThemeEditor.names) || [];

  names.forEach((name) => {
    registerBlockType(name, {
      edit: function Edit() {
        const blockProps = useBlockProps();
        return el('div', blockProps, el(ServerSideRender, { block: name }));
      },
      save: function Save() {
        return null;
      },
    });
  });
})(
  window.wp.blocks,
  window.wp.element,
  window.wp.blockEditor,
  window.wp.serverSideRender
);
