<?php

use App\Services\AnswerGrader;

it('ignores spacing, indentation and capital letters in HTML answers', function () {
    $g = new AnswerGrader;
    $ref = '<!DOCTYPE html><html><head><title>Student Page</title></head><body><p>Welcome to HTML</p></body></html>';

    expect($g->matches("<!DOCTYPE html>\n<html>\n  <head>\n    <title>Student Page</title>\n  </head>\n  <body>\n    <P>Welcome to HTML</P>\n  </body>\n</html>", $ref))->toBeTrue();
    expect($g->matches('<html lang="en"><head><meta charset="UTF-8"><title>Student Page</title></head><body><p>Welcome to HTML</p></body></html>', $ref))->toBeTrue();
    expect($g->matches('<html><head><title>Student Page</title></head><body><p>Hello</p></body></html>', $ref))->toBeFalse();
});

it('compares tables, lists and media tags by structure', function () {
    $g = new AnswerGrader;

    expect($g->matches('<ol> <li>HTML</li> <li>CSS</li> <li>JavaScript</li> </ol>', '<ol><li>HTML</li><li>CSS</li><li>JavaScript</li></ol>'))->toBeTrue();
    expect($g->matches('<ul><li>HTML</li><li>CSS</li><li>JavaScript</li></ul>', '<ol><li>HTML</li><li>CSS</li><li>JavaScript</li></ol>'))->toBeFalse();
    expect($g->matches('<table><thead><tr><th>Name</th><th>Roll No</th></tr></thead><tbody><tr><td>Asha</td><td>12</td></tr></tbody></table>', '<table><tr><th>Name</th><th>Roll No</th></tr><tr><td>Asha</td><td>12</td></tr></table>'))->toBeTrue();
    expect($g->matches('<video width="400" height="300" controls loop src="myVid.mp4"></video>', '<video src="myVid.mp4" controls width="400" height="300" loop></video>'))->toBeTrue();
    expect($g->matches('<video src="myVid.mp4" controls width="400" height="300"></video>', '<video src="myVid.mp4" controls width="400" height="300" loop></video>'))->toBeFalse();
});

it('handles plain text answers and alternatives', function () {
    $g = new AnswerGrader;

    expect($g->matches('  New   YORK. ', 'New York'))->toBeTrue();
    expect($g->matches('NewYork', 'New York'))->toBeFalse();
    expect($g->matches('colour', 'color || colour'))->toBeTrue();
    expect($g->matches('||', '||', false))->toBeTrue();
    expect($g->matches('&&', '||', false))->toBeFalse();
});

it('compares program output while ignoring line endings and trailing spaces', function () {
    $g = new AnswerGrader;

    expect($g->outputMatches("Hello  \r\n5\r\n\r\n", "Hello\n5"))->toBeTrue();
    expect($g->outputMatches("Hello\n6", "Hello\n5"))->toBeFalse();
});
