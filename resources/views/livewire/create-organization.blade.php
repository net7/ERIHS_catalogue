<div class="items-center justify-center">
    <div class="max-w-xl mx-auto border-solid p-5">
         {{-- x-data="{
        errors: @entangle('errors').live,
        focusError() {
            es = Object.keys(this.errors);
            if (es.length == 0) { return }
            $focus.focus(document.getElementsByName(es[0])[0]);
          }
        }"
        x-init="$watch('errors', value => focusError())"
        > --}}
        <form wire:submit="submit" >
            {{ $this->providerForm }}
            <p>&nbsp;</p>
            {{ $this->addressForm }}

            <button type="submit" class="w-full bg-blue-500 text-white rounded-md mt-5 py-2.5">
                    Submit
            </button>
        </form>
    </div>


</div>
