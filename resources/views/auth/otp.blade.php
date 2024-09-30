@extends('components.layout')

@section('title', 'Enter OTP')

@section('content')
    <div id="otp-app" class="flex justify-center items-center min-h-screen bg-gray-100">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md mx-auto">
            <h2 class="text-2xl font-bold mb-6 text-center">Check your email & Enter OTP</h2>
            <form @submit.prevent="submitOtp">
                @csrf
                <div class="flex justify-center space-x-2 mb-6">
                    <div v-for="(digit, index) in digits" :key="index">
                        <input type="text" maxlength="1" v-model="digits[index]" :ref="'digit' + index"
                            class="w-12 h-12 text-center text-2xl border border-gray-300 rounded-md focus:border-green-500 focus:ring focus:ring-green-500 focus:ring-opacity-50"
                            @input="moveNext(index)" @keydown.backspace="movePrev(index)" pattern="[0-9]"
                            inputmode="numeric">
                    </div>
                </div>
                <input type="hidden" name="otp" :value="otp">
                <div v-if="errors.length" class="mb-4 text-red-600">
                    <ul>
                        <li v-for="(error, index) in errors" :key="index">@{{ error }}</li>
                    </ul>
                </div>
                <div v-if="status" class="mb-4 text-green-600">
                    @{{ status }}
                </div>
                <div class="flex justify-between items-center mb-4">
                    <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Submit
                    </button>
                    <button type="button"
                        class="text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                        :class="countdown > 0 ? 'bg-gray-400' : 'bg-green-600 hover:bg-green-700'" :disabled="countdown > 0"
                        @click="resendOtp">
                        @{{ countdown > 0 ? `Resend in ${countdown}s` : 'Resend OTP' }}
                    </button>
                </div>
                <div class="text-gray-600 text-center" v-if="countdown <= 0">
                    Didn't receive any code? Check your spam folder or <span class="text-green-600">request a new
                        OTP.</span>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        new Vue({
            el: '#otp-app',
            data: {
                digits: ['', '', '', ''],
                countdown: 60,
                otp: '',
                errors: [],
                status: ''
            },
            created() {
                this.startCountdown();
            },
            methods: {
                moveNext(index) {
                    if (this.digits[index].length === 1 && index < this.digits.length - 1) {
                        this.$refs['digit' + (index + 1)][0].focus();
                    }
                    this.otp = this.digits.join('');
                },
                movePrev(index) {
                    if (index > 0 && this.digits[index].length === 0) {
                        this.$refs['digit' + (index - 1)][0].focus();
                    }
                    this.otp = this.digits.join('');
                },
                startCountdown() {
                    if (this.countdown > 0) {
                        setTimeout(() => {
                            this.countdown--;
                            this.startCountdown();
                        }, 1000);
                    }
                },
                async resendOtp() {
                    try {
                        const response = await fetch('{{ route('resend.otp') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });

                        if (!response.ok) {
                            throw new Error('Failed to resend OTP.');
                        }

                        const data = await response.json();

                        this.status = data.status || 'A new OTP has been sent to your email.';
                        this.errors = [];
                        this.digits = ['', '', '', '']; // Clear input fields
                        this.otp = ''; // Clear the OTP
                        this.countdown = 60;
                        this.startCountdown();
                        Swal.fire({
                            icon: 'success',
                            title: 'OTP Resent',
                            text: 'A new OTP has been sent to your email.',
                        });
                    } catch (error) {
                        console.error('Error resending OTP:', error);
                        this.errors.push('Failed to resend OTP. Please try again.');
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to resend OTP. Please try again.',
                        });
                    }
                },
                async submitOtp() {
                    try {
                        const response = await fetch('{{ route('validate.otp') }}', {
                            method: 'POST',
                            headers: {
                                "Accept": "application/json",
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                otp: this.otp
                            })
                        });

                        if (!response.ok) {
                            const errorData = await response.json();
                            this.errors = errorData.errors || ['Failed to submit OTP. Please try again.'];
                            Swal.fire({
                                icon: 'error',
                                title: 'Invalid OTP',
                                text: 'Please check your email for the correct OTP.',
                            });
                            return;
                        }

                        const data = await response.json();

                        if (data.errors) {
                            this.errors = data.errors;
                            Swal.fire({
                                icon: 'error',
                                title: 'Invalid OTP',
                                text: 'Please check your email for the correct OTP.',
                            });
                        } else if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            this.status = data.status;
                            Swal.fire({
                                icon: 'success',
                                title: 'OTP Verified',
                                text: 'Your OTP has been successfully verified!',
                            });
                        }
                    } catch (error) {
                        console.error('Error submitting OTP:', error);
                        this.errors.push('Failed to submit OTP. Please try again.');
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to submit OTP. Please try again.',
                        });
                    }
                }
            }
        });
    </script>
@endsection
