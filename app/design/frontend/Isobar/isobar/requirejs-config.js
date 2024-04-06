var config = {
    map: {
        '*': {
            carousel: 'js/slider',
            owlcarousel: "js/owl.carousel"
        }
    },
    paths: {
        'owlcarousel': 'js/owl.carousel'
    },
    shim: {
        'owlcarousel': {
            deps: ['jquery']
        }
    },
    deps: [
        "js/menu-custom"
    ]
}