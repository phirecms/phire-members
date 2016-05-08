/**
 * Members Module Scripts for Phire CMS 2
 */

jax(document).ready(function(){
    if (jax('#members-admins-form')[0] != undefined) {
        jax('#checkall').click(function(){
            if (this.checked) {
                jax('#members-admins-form').checkAll(this.value);
            } else {
                jax('#members-admins-form').uncheckAll(this.value);
            }
        });
        jax('#members-admins-form').submit(function(){
            return jax('#members-admins-form').checkValidate('checkbox', true);
        });
    }
});