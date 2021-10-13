import bootstrap from 'bootstrap';
import config from './config';
import { seed, storage } from './API';

export default async () => {
	if (storage.isEmpty() || config.mode === 'development') {
		storage.clear();
		await seed();
	}
};
