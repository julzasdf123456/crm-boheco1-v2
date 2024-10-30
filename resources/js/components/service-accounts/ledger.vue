<template>
    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover">
                    <thead>
                        <th>Invoice No.</th>
                        <th>Billing Month</th>
                        <th class="text-right">Prev. Reading</th>
                        <th class="text-right">Pres. Reading</th>
                        <th class="text-right">kWH</th>
                        <th class="text-right">Multiplier</th>
                        <th class="text-right">Total kWH<br>Consumed</th>
                        <th class="text-right">Net Amount</th>
                        <th>Due Date</th>
                        <th>OR Number</th>
                        <th>Payment Date</th>
                        <th>Cashier</th>
                        <th></th>
                    </thead>
                    <tbody>
                        <tr v-for="bill in ledger" :key="bill.BillNumber" :class="isNull(bill.Teller) ? 'bg-danger' : ''">
                            <td class="v-align">
                                <i class="fas fa-check-circle text-success ico-tab-mini" v-if="!isNull(bill.Teller)"></i>
                                <i class="fas fa-exclamation-triangle ico-tab-mini" v-if="isNull(bill.Teller)"></i>
                                {{ bill.BillNumber }}
                            </td>
                            <td class="v-align">{{ moment(bill.ServicePeriodEnd).format("MMMM YYYY") }}</td>
                            <td class="v-align text-right">{{ bill.PowerPreviousReading }}</td>
                            <td class="v-align text-right">{{ bill.PowerPresentReading }}</td>
                            <td class="v-align text-right">{{ getKwhUsed(bill.PowerPreviousReading, bill.PowerPresentReading) }}</td>
                            <td class="v-align text-right">{{ meterInfo.Multiplier }}</td>
                            <td class="v-align text-right">{{ bill.PowerKWH }}</td>
                            <td class="v-align text-right">{{ toMoney(parseFloat(bill.NetAmount)) }}</td>
                            <td class="v-align">{{ isNull(bill.DueDate) ? '-' : moment(bill.DueDate).format("MMM DD, YYYY") }}</td>
                            <td class="v-align text-right">{{ bill.ORNumber }}</td>
                            <td class="v-align">{{ isNull(bill.PostingDate) ? '-' : moment(bill.PostingDate).format("MMM DD, YYYY") }}</td>
                            <td class="v-align">{{ isNull(bill.Teller) ? '-' : bill.Teller }}</td>
                            <td class="v-align text-right">
                                <a class="btn btn-link-muted btn-sm" href="" title="Edit/adjust bill"><i class="fas fa-pen"></i></a>
                                <a class="btn btn-link-muted btn-sm" href="" title="Print sales invoice"><i class="fas fa-print"></i></a>

                                <div title="More Options" style="display: inline;">
                                    <a class="btn btn-link-muted btn-sm" href="#" role="button" data-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </a>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item"><i class="fas fa-clock ico-tab"></i>Move Due Date</a>
                                        <button @click="createPDFandSendMail(moment(bill.ServicePeriodEnd).format('YYYY-MM-DD'))" class="dropdown-item"><i class="fas fa-paper-plane ico-tab"></i>Send Bill via Email</button>
                                        <a class="dropdown-item"><i class="fas fa-file-invoice-dollar ico-tab"></i>View Payment Details</a>

                                        <div class="divider"></div>

                                        <button v-if="!isNull(bill.Teller)" title="Remove/cancel payment" class='dropdown-item text-danger'><i class="fas fa-times ico-tab"></i>Cancel Payment</button>
                                        <button title="Delete this bill" class='dropdown-item text-danger'><i class="fas fa-trash ico-tab"></i>Cancel Bill</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <pagination :data="rawLedger" :limit="10" @pagination-change-page="getBillsLedger"></pagination>
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
import jquery from 'jquery';
import Swal from 'sweetalert2';
import { renderSlot } from 'vue';

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
            billPDFs : window.location.origin + axios.defaults.billPDFs,
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
            ledger : [],
            rawLedger : {},
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
        getBillsLedger(page = 1) {
            axios.get(`${ this.baseURL }/account_masters/get-bills-ledger`, {
                params: {
                    page : page,
                    AccountNumber : this.accountNumber
                }
            }).then(response => {
                this.ledger = response.data.BillsLedger.data
                this.rawLedger = response.data.BillsLedger
                this.meterInfo = response.data.MeterInfo
            })
            .catch(error => {
                Swal.fire({
                    icon : 'error',
                    title : 'Error getting bill ledger!',
                });
                console.log(error.response)
            });
        },
        getKwhUsed(prev, pres) {
            if (this.isNull(prev)) {
                prev = 0
            }

            if (this.isNull(pres)) {
                pres = 0
            }

            return pres - prev
        },
        createPDFandSendMail(period) {
            axios.get(`${ this.baseURL }/bills/create-pdf-bill`, {
                params: {
                    BillingMonth : period,
                    AccountNumber : this.accountNumber
                }
            }).then(response => {
                // start forwarding to automailer.boheco1.com
                const filePath = this.billPDFs + response.data

                axios.get(filePath, {
                    responseType: 'blob', // Fetch the file as a Blob
                })
                .then(res => {
                    const formData = new FormData();
                    formData.append("file", res.data, "SOA.pdf"); // Specify file name here

                    // Add any additional parameters if needed
                    formData.append("Subject", "TEST BILL");
                    formData.append("Body", "TEST BILL");
                    formData.append("Recipient", "julzasdf23456@gmail.com");

                    // Send the POST request with FormData
                    axios.post("https://automailer.boheco1.com/forward-bill.php", formData, {
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                    })
                    .then(rx => {
                        alert("Success: " + response.data)
                    })

                    
                })

                
            })
            .catch(error => {
                Swal.fire({
                    icon : 'error',
                    title : 'Error sending mail!',
                });
                console.log(error.response)
            });
        }
    },
    created() {
        
    },
    mounted() {
        this.getBillsLedger()
    }
}

</script>