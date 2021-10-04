import _bootstrap from 'bootstrap';
import { seedLocalStorage } from './API';
import x from './VaccineFetcher';

seedLocalStorage().then(x());

console.log(localStorage);
