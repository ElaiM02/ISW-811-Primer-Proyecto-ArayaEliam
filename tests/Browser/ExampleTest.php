<?php

it('returns a successful response', function(){
    visit('/')->assertSee('Welcome');
});