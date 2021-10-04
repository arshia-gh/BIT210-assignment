import _bootstrap, {Modal} from 'bootstrap';
import { seedLocalStorage } from './API';
import x from './VaccineFetcher';

seedLocalStorage().then(x());

console.log(localStorage);
new Modal(document.getElementById('AddBatchModal')).show();