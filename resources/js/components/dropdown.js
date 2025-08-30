export default () => ({
    open: false,

    dropdownTrigger: {
      ['@click']() {
        this.open = ! this.open
      },
      ['@click.outside']() {
        this.open = false
      }
    },

    content: {
      ['x-show']() {
        return this.open
      },
      //This applies the fading style as Tailwind CSS rather than having to do it inline.
      ['x-transition:enter']: "transition ease-out duration-[400ms]",
      ['x-transition:enter-start']: "transform opacity-0 scale-100",
      ['x-transition:enter-end']: "transform opacity-100 scale-100",
      ['x-transition:leave']: "transition ease-out duration-[400ms]",
      ['x-transition:leave-start']: "transform opacity-100 scale-100",
      ['x-transition:leave-end']: "transform opacity-0 scale-100",
    },
})
