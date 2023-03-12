function docReady(fn) {
    if (document.readyState === "complete" || document.readyState === "interactive") {
        setTimeout(fn, 1);
    } else {
        document.addEventListener("DOMContentLoaded", fn);
    }
} 

docReady(function() {
    
    if(BigMasterWebCHT_settings.lazy_load){
        lazyLoadInstance = new LazyLoad({
            elements_selector: '[data-lazyload]',
            load_delay: 0,
        }); 
    }
});