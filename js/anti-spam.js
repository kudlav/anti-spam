/*
Anti-spam Reloaded plugin
*/

'use strict';
(function() {
	function anti_spam_reloaded_init() {

		// hide inputs from users
		let elements = document.querySelectorAll('.antispamrel-group');
		for (let i = 0; i < elements.length; i++) {
			elements[i].style.display = 'none';
		}

		// get the answer
		let answer = '';
		const controlA = document.querySelector('.antispamrel-control-a');
		if (controlA !== null) {
			answer = controlA.value;
		}

		// set answer into other input instead of user
		elements = document.querySelectorAll('.antispamrel-control-q');
		for (let i = 0; i < elements.length; i++) {
			elements[i].value = answer;
		}

		// clear value of the empty input because some themes are adding some value for all inputs
		elements = document.querySelectorAll('.antispamrel-control-e');
		for (let i = 0; i < elements.length; i++) {
			elements[i].value = '';
		}

		//dynamic_control = '<input type="text" name="antspmrl-d" class="antispamrel-control-d" value="' + current_year + '" />';
		let dynamic_control = document.createElement('input');
		dynamic_control.setAttribute('type', 'hidden');
		dynamic_control.setAttribute('name', 'antspmrl-d');
		dynamic_control.setAttribute('class', 'antispamrel-control-d');
		dynamic_control.setAttribute('value', new Date().getFullYear().toString());

		// add input for every comment form if there are more than 1 form with IDs: comments, respond or commentform
		elements = document.querySelectorAll('form');
		for (let i = 0; i < elements.length; i++) {
			if ( (elements[i].id === 'comments') || (elements[i].id === 'respond') || (elements[i].id === 'commentform') ) {
				let class_index = elements[i].className.indexOf('anti-spam-reloaded-form-processed');
				if ( class_index === -1 ) { // form is not yet js processed
					elements[i].appendChild(dynamic_control);
					elements[i].className = elements[i].className + ' anti-spam-reloaded-form-processed';
				}
			}
		}
	}

	document.addEventListener('DOMContentLoaded', anti_spam_reloaded_init, false);

})();
