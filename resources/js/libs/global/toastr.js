import toastr from 'toastr';
import 'toastr/build/toastr.min.css';

toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: 'toast-top-right',
    timeOut: 30000,
};

window.toastr = toastr;

