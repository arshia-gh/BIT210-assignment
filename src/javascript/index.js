import main from './main';
import AuthForm from './module/AuthForm';

document.addEventListener('DOMContentLoaded', async () => {
	await main();
	const authForm = await new AuthForm().init();
});
