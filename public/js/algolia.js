// <!-- Initialize autocomplete menu -->

var client = algoliasearch('PAFMSYR5OM', 'f6ffab4d3a28c2dd99d989d0993f1d21');
var index = client.initIndex('products');
//initialize autocomplete on search input (ID selector must match)
autocomplete('#aa-search-input',
{ hint: false }, {
    source: autocomplete.sources.hits(index, {hitsPerPage: 5}),
    //value to be displayed in input control after user's suggestion selection
    displayKey: 'name',
    //hash of templates used when rendering dataset
    templates: {
        //'suggestion' templating function used to render a single suggestion
        suggestion: function(suggestion) {
          
            const markup = `
                <div class="algolia-result">
                <span>
                    <img src="${window.location.origin}/${suggestion.image}" alt="Image" class="algolia-thumb"> 
                    ${suggestion._highlightResult.name.value}
                </span>
                <span>$${ (suggestion.price / 100).toFixed(2) }</span>
                </div>
                <div class="algolia-details">
                    <span>${ suggestion._highlightResult.details.value }</span>
                </div>
            `;
            return markup;
            // return '<span>' +
            // suggestion._highlightResult.name.value + '</span><span>' +
            // suggestion.price + '</span>';
        },
        empty: function(result){
            return 'Sorry, We did not find any result for "' + result.query + '"';
        }
    }
}).on('autocomplete:selected', function(event, suggestion, dataset){
    // console.log(suggestion);
    window.location.href = window.location.origin + '/shop/' + suggestion.slug;
});