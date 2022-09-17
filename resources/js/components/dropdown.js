export default () => ({
    open: false,

    trigger: {
      ['@click']() {
        this.open = ! this.open
      },
    },

    toggle: {
      ['x-show']() {
        return this.open
      },
    },
})
