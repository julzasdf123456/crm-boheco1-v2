<template>
    <div class="row">
        <!-- ACCOUNT PROFILE -->
        <div class="col-lg-12">
            <div class="card shadow-none" style="background: linear-gradient(to right, #17bf87, #c4bd5d);">
                <div class="card-body">
                    <div>
                        <span class="badge float-right" :class="accountData.AccountStatus === 'ACTIVE' ? 'bg-success' : 'bg-danger'">{{ accountData.AccountStatus }}</span>
                        <img :src="imgURL + '/prof-icon.png'" style="width: 80px; height: 80px; margin-right: 15px; border: 4px solid white;" class="img-circle" alt="profile">
                        <h3 class="text-white mt-2"><strong>{{ accountData.ConsumerName }}</strong></h3>
                        <p class="no-pads text-white">{{ accountData.AccountNumber }} • {{ accountData.ConsumerAddress }} • {{ accountData.ConsumerType }}</p>

                        <div class="mt-2">
                            <a class="btn btn-sm btn-floating">Edit Account</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABS -->
        <div class="col-lg-12">
            <div class="card shadow-none">
                <div class="card-body">
                    <div>
                        <!-- TAB HEADS -->
                        <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="ledger-tab" data-toggle="pill" href="#ledger-content" role="tab" aria-controls="ledger-content" aria-selected="false">Ledger</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="account-details-tab" data-toggle="pill" href="#account-details-content" role="tab" aria-controls="account-details-content" aria-selected="false">Account Details</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="reading-tab" data-toggle="pill" href="#reading-content" role="tab" aria-controls="reading-content" aria-selected="false">Reading History</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="prepayments-tab" data-toggle="pill" href="#prepayments-content" role="tab" aria-controls="prepayments-content" aria-selected="false">Prepayments & Deposits</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tickets-tab" data-toggle="pill" href="#tickets-content" role="tab" aria-controls="tickets-content" aria-selected="false">Ticket History</a>
                            </li>
                        </ul>

                        <!-- TAB CONTENT -->
                        <div class="tab-content" id="custom-tabs-three-tabContent">
                            <div class="tab-pane fade active show" id="ledger-content" role="tabpanel" aria-labelledby="ledger-tab">
                                <ledger></ledger>
                            </div>

                            <div class="tab-pane fade" id="account-details-content" role="tabpanel" aria-labelledby="account-details-tab">
                                <div class="row mt-4 px-3">
                                    <!-- BASIC INFO -->
                                    <div class="col-lg-6">
                                        <p class="text-muted"><i class="fas fa-info-circle ico-tab"></i><strong>Basic Account Information</strong></p>
                                        <div class="table-responsive">
                                            <table class="table table-hover table-sm table-bordered">
                                                <tbody>
                                                    <tr>
                                                        <td class="text-muted v-align">Account ID</td>
                                                        <td class="v-align">{{ accountData.UniqueID }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted v-align">Area</td>
                                                        <td class="v-align">{{ accountData.Area }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted v-align">Route</td>
                                                        <td class="v-align">{{ accountData.Route }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted v-align">Sequence Number</td>
                                                        <td class="v-align">{{ accountData.SequenceNumber }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted v-align">Compute Mode</td>
                                                        <td class="v-align">{{ accountData.ComputeMode }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted v-align">Group Tag</td>
                                                        <td class="v-align">{{ accountData.GroupTag }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted v-align">Billing Status</td>
                                                        <td class="v-align">{{ accountData.BillingStatus }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted v-align">Contact Numbers</td>
                                                        <td class="v-align">{{ accountData.ContactNumber }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted v-align">Email Address</td>
                                                        <td class="v-align">{{ accountData.Email }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- TECHNICAL -->
                                    <div class="col-lg-6">
                                        <p class="text-muted"><i class="fas fa-tachometer-alt ico-tab"></i><strong>Technical Information</strong></p>
                                        <div class="table-responsive">
                                            <table class="table table-hover table-sm table-bordered">
                                                <tbody>
                                                    <tr>
                                                        <td class="text-muted v-align">Meter Number</td>
                                                        <td>{{ meterInfo.MeterNumber }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted v-align">Meter Brand</td>
                                                        <td>{{ meterInfo.Make }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted v-align">Meter Multiplier</td>
                                                        <td>{{ meterInfo.Multiplier }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted v-align">Feeder</td>
                                                        <td>{{ accountData.Feeder }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted v-align">Pole Number</td>
                                                        <td>{{ accountData.Pole }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted v-align">Transformer Number</td>
                                                        <td>{{ accountData.Transformer }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted v-align">Coreloss</td>
                                                        <td>{{ accountData.CoreLoss }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted v-align">Upper Limit</td>
                                                        <td>{{ accountData.CoreLossKWHUpperLimit }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted v-align">Transformer Rental</td>
                                                        <td>{{ accountData.TSFRental }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted v-align">GPS Coordinates</td>
                                                        <td>{{ accountData.Item1 }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- LOGS -->
                                    <div class="col-lg-6">
                                        <p class="text-muted"><i class="fas fa-plug ico-tab"></i><strong>Connection Information</strong></p>
                                        <div class="table-responsive">
                                            <table class="table table-hover table-sm table-bordered">
                                                <tbody>
                                                    <tr>
                                                        <td class="text-muted v-align">Svc. Application No.</td>
                                                        <td>{{ accountData.TurnOnNumber }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted v-align">Connection Date</td>
                                                        <td class="v-align">{{ isNull(accountData.ConnectionDate) ? '' : moment(accountData.ConnectionDate).format("MMMM DD, YYYY") }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted v-align">Lineman Installer</td>
                                                        <td>{{ accountData.InstalledBy }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted v-align">Encoder</td>
                                                        <td>{{ accountData.UserName }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted v-align">Date Encoded</td>
                                                        <td>{{ isNull(accountData.DateEntry) ? '' : moment(accountData.DateEntry).format("MMMM DD, YYYY") }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="tab-pane fade" id="reading-content" role="tabpanel" aria-labelledby="reading-tab">
                                <reading-history></reading-history>
                            </div>

                            <div class="tab-pane fade" id="prepayments-content" role="tabpanel" aria-labelledby="prepayments-tab">

                            </div>
                            
                            <div class="tab-pane fade" id="tickets-content" role="tabpanel" aria-labelledby="tickets-tab">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import moment from 'moment';
import FlatPickr from 'vue-flatpickr-component';
import { Bootstrap4Pagination } from 'laravel-vue-pagination'
import 'flatpickr/dist/flatpickr.css';
import Swal from 'sweetalert2';

export default {
    components : {
        FlatPickr,
        Swal,
        'pagination' : Bootstrap4Pagination
    },
    data() {
        return {
            moment : moment,
            baseURL : window.location.origin + axios.defaults.baseURL,
            imgURL : window.location.origin + axios.defaults.imgURL,
            colorProfile : document.querySelector("meta[name='color-profile']").getAttribute('content'),
            tableInputTextColor : this.isNull(document.querySelector("meta[name='color-profile']").getAttribute('content')) ? 'text-dark' : 'text-white',
            accountNumber : document.querySelector("meta[name='accountNumber']").getAttribute('content'),
            pickerOptions: {
                enableTime: false, // Enable time selection
                dateFormat: 'Y-m-d', // Date format
            },
            toast : Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            }),
            accountData : {},
            meterInfo : {},
        }
    },
    methods : {
        isNull (value) {
            // Check for null or undefined
            if (value === null || value === undefined) {
                return true;
            }

            // Check for empty string
            if (typeof value === 'string' && value.trim() === '') {
                return true;
            }

            // Check for empty array
            if (Array.isArray(value) && value.length === 0) {
                return true;
            }

            // Check for empty object
            if (typeof value === 'object' && !Array.isArray(value) && Object.keys(value).length === 0) {
                return true;
            }

            // Check for NaN
            if (typeof value === 'number' && isNaN(value)) {
                return true;
            }

            // If none of the above, it's not null, empty, or undefined
            return false;
        },
        toMoney(value) {
            if (this.isNumber(value)) {
                return Number(parseFloat(value).toFixed(2)).toLocaleString("en-US", { maximumFractionDigits: 2, minimumFractionDigits: 2 })
            } else {
                return '-'
            }
        },
        isNumber(value) {
            return typeof value === 'number';
        },        
        round(value) {
            return Math.round((value + Number.EPSILON) * 100) / 100;
        },
        generateRandomString(length) {
            const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            let result = '';

            for (let i = 0; i < length; i++) {
                const randomIndex = Math.floor(Math.random() * characters.length);
                result += characters.charAt(randomIndex);
            }

            return result;
        },
        generateUniqueId() {
            return moment().valueOf() + "-" + this.generateRandomString(32);
        },
        getAccountInformation() {
            axios.get(`${ this.baseURL }/account_masters/get-account-information`, {
                params: {
                    AccountNumber : this.accountNumber
                }
            }).then(response => {
                this.accountData = response.data.AccountData
                this.meterInfo = response.data.MeterInfo
            })
            .catch(error => {
                Swal.fire({
                    icon : 'error',
                    title : 'Error getting account information!',
                });
                console.log(error.response)
            });
        },
    },
    created() {
        
    },
    mounted() {
        this.getAccountInformation()
    }
}

</script>