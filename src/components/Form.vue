<template>
	<div>
		<loading :active="sending" :is-full-page="true"></loading>

		<ValidationObserver ref="observer" v-slot="{ handleSubmit }">
			<form class="form_wrap" @submit.prevent="handleSubmit(sendSMS)">
				<template v-if="alert">
					<div v-if="alert.status" class="alert alert-success">{{alert.message}}</div>
					<div v-else class="alert alert-danger">{{alert.message}}</div>
				</template>

				<h1>Send an SMS!</h1>
				<p>Type a number and message</p>

				<div class="form-group">
					<ValidationProvider rules="required" name="phone" v-slot="{errors}">
						<cleave
							type="tel"
							v-model="sms.phone"
							:options="phoneoptions"
							class="form-control"
							placeholder="Phone number"
						></cleave>

						<span>{{errors[0]}}</span>
					</ValidationProvider>
				</div>

				<div class="form-group">
					<ValidationProvider rules="required" name="message" v-slot="{errors}">
						<textarea v-model="sms.message" class="form-control" placeholder="Your message"></textarea>
						<span>{{errors[0]}}</span>
					</ValidationProvider>
					<small v-if="sms.message">{{messageLength}} characters left</small>
				</div>

				<div class="form-group">
					<button type="submit" class="btn btn-success btn-lg btn-block">Send SMS</button>
				</div>
			</form>
		</ValidationObserver>
	</div>
</template>


<script>
import { parsePhoneNumberFromString } from "libphonenumber-js";

import {
	ValidationProvider,
	ValidationObserver
} from "vee-validate/dist/vee-validate.full";

import Cleave from "vue-cleave-component";
import "cleave.js/dist/addons/cleave-phone.au.js";

import Loading from "vue-loading-overlay";
import "vue-loading-overlay/dist/vue-loading.css";

export default {
	components: {
		ValidationObserver,
		ValidationProvider,
		Cleave,
		Loading
	},

	data() {
		return {
			sms: {
				phone: null,
				formattedPhone: null,
				message: null
			},

			phoneoptions: {
				phone: true,
				phoneRegionCode: "AU"
			},

			alert: null,
			sending: false
		};
	},

	methods: {
		sendSMS() {
			if (this.sms.message.length > 160) {
				this.sendError(false, "Please limit your message to 160 characters");

				return;
			}

			const phoneNumber = parsePhoneNumberFromString(this.sms.phone, "AU");

			if (!phoneNumber.isValid()) {
				this.sendError(false, "Phone number is not valid");

				return;
			}

			this.sms.formattedPhone = phoneNumber.number;

			this.sending = true;

			axios
				.post("sendsms.php", this.sms)
				.then(res => {
					this.$refs.observer.reset();
					this.sending = false;
					this.alert = res.data;
					this.resetForm();
				})
				.catch(err => {
					console.log(err.response);
				});
		},

		resetForm() {
			this.sms = {
				phone: null,
				formattedPhone: null,
				message: null
			};
		},

		sendError(status, message) {
			this.alert = {
				status: status,
				message: message
			};
		}
	},

	computed: {
		messageLength() {
			var limit = 160;
			var length = limit - this.sms.message.length;

			if (length < 0) {
				return 0;
			}

			return length;
		}
	}
};
</script>