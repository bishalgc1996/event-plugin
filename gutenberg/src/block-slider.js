const {__} = wp.i18n;
const {registerBlockType} = wp.blocks;


const { InspectorControls } = wp.blockEditor;

const { Placeholder,PanelBody, RangeControl } = wp.components;
const BlockEdit = (props) => {
    const { attributes, setAttributes } = props;


    return(
        <div className={props.className}>
            <InspectorControls>
                <PanelBody
                    title={__('Slider Settings', 'awp')}
                    initialOpen={true}
                >
                    <RangeControl
                        label={__('Number of slides', 'awp')}
                        value={attributes.numSlides}
                        onChange={(val) => setAttributes({ numSlides: val })}
                        min={1}
                        max={10}
                    />
                </PanelBody>
            </InspectorControls>
            <Placeholder label={__('Slider ', 'ep')}>
            </Placeholder>
        </div>
    );
}


registerBlockType('ep/slider', {
    title: __('Event Pugin Slider', 'ep'),
    icon: 'slides',
    category: 'common',
    supports: {
        align: ['center', 'wide', 'full']
    },
    attributes: {
        align: {
            type: 'string',
            default: 'center'
        },
        numSlides: {
            type: 'number',
            default: 3
        },
    },
    edit: BlockEdit,
    save: () => {
        return null;
    }
});


