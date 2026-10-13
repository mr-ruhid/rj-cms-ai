@verbatim
<script>
function builder() {
    return Object.assign(
        builderState(),
        builderActions(),
        builderRender(),
        builderPanels(),
    );
}
</script>
@endverbatim