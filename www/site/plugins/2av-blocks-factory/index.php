<?php
Kirby::plugin('2av/blocks-factory', [
  'blueprints' => [
    'blocks/accordion_element'   => __DIR__ . '/blueprints/blocks/accordion_element.yml',
    'blocks/accordion'         => __DIR__ . '/blueprints/blocks/accordion.yml',
    'blocks/toast'         => __DIR__ . '/blueprints/blocks/toast.yml',
    'blocks/card'        => __DIR__ . '/blueprints/blocks/card.yml',
    'blocks/testimonial' => __DIR__ . '/blueprints/blocks/testimonial.yml',
  ],
  'snippets' => [
    'blocks/accordion_element'   => __DIR__ . '/snippets/blocks/accordion_element.php',
    'blocks/accordion'         => __DIR__ . '/snippets/blocks/accordion.php',
    'blocks/toast'         => __DIR__ . '/snippets/blocks/toast.php',
    'blocks/card'        => __DIR__ . '/snippets/blocks/card.php',    
    'blocks/testimonial' => __DIR__ . '/snippets/blocks/testimonial.php',
  ],
  'translations' => [
    'en' => [
      'field.blocks.accordion_element.name'   => 'Accordion Element block',
      'field.blocks.accordion.name'         => 'Accordion Section',
      'field.blocks.toast.name'         => 'Toast block',
      'field.blocks.card.name'        => 'Card',      
      'field.blocks.testimonial.name' => 'Testimonial',
    ],
    'de' => [
      'field.blocks.accordion_element.name'   => 'Accordion Element block',
      'field.blocks.accordion.name'         => 'Accordion Section',
      'field.blocks.toast.name'         => 'Toast block',
      'field.blocks.card.name'        => 'Card',      
      'field.blocks.testimonial.name' => 'Testimonial',
    ]
  ],
]);