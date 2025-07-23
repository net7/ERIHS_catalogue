import './bootstrap';

// import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';

import Focus from '@alpinejs/focus'
import AlpineFloatingUI from '@awcodes/alpine-floating-ui'
import Tooltip from "@ryangjchandler/alpine-tooltip";

Alpine.plugin(Focus)
Alpine.plugin(AlpineFloatingUI)
Alpine.plugin(Tooltip);


Livewire.start()