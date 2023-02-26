{if $available}
    <div class="lm-add-to-cart-btn-wrapper">
    <form action="{$urls.pages.cart}" method="post" id="add-to-cart-or-refresh">
            <input type="hidden" name="id_product" value="{$id_product}" id="product_page_product_id"> 
            <button class="btn btn-primary lm-add-to-cart" data-button-action="add-to-cart" data-controller-url={$controller_url} type="submit">
            {l s='Add to cart' d='Shop.Theme.Actions'}
            </button>
        </form>
    </div>
    {else}
        <button class="btn disabled btn-secondary">
        {l s='Add to cart' d='Shop.Theme.Actions'}
        </button>
{/if}
