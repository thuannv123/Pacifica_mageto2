var config = {
    map: {
        '*': {
            carousel: 'js/sliderJS',
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
        "js/handle"
    ]
}