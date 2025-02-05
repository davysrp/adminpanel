import './bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import Clipboard from '@ryangjchandler/alpine-clipboard'

Alpine.plugin(Clipboard)

Livewire.start()
