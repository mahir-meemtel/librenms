/*
 * boot.js
 *
 * Initialize javascript for ObzoraNMS v1
 *
 */

// set CSRF for jquery ajax request
$.ajaxSetup({
    headers:
        { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

// toastr style to match php toasts
toastr.options = {
    toastClass: 'tw:border-current tw:relative tw:pl-20 tw:py-4 tw:pr-2 tw:bg-white! tw:dark:bg-dark-gray-300! tw:opacity-80 tw:hover:opacity-100 tw:rounded-md tw:shadow-lg tw:hover:shadow-xl tw:border-l-8 tw:border-t-0.5 tw:border-r-0.5 tw:border-b-0.5 tw:mt-2 tw:cursor-pointer',
    titleClass: 'tw:text-xl tw:leading-7 tw:font-semibold tw:capitalize',
    messageClass: 'tw:mt-1 tw:text-base tw:leading-5 tw:text-gray-500 tw:dark:text-white',
    iconClasses: {
        error: 'toast-error tw:text-red-600 tw:border-red-600',
        info: 'toast-info tw:text-blue-600 tw:border-blue-600',
        success: 'toast-success tw:text-green-600 tw:border-green-600',
        warning: 'toast-warning tw:text-yellow-600 tw:border-yellow-600'
    },
    timeOut: 12000,
    progressBar: true,
    progressClass: 'toast-progress tw:h-1 tw:bg-current tw:absolute tw:bottom-0 tw:left-0 tw:mr-0.5',
    containerId: 'toast-container-top-right'
};
