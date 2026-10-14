<script>
function builder() {
    return Object.assign(
        builderState(),
        builderActions(),
        builderRender(),
        builderPanels(),
        builderFields(),
    );
}
</script>