USE vintage_newspaper;

-- Sample articles
INSERT INTO articles (title, slug, content,excerpt,category_id, author_id , status) VALUES
(
    'The Future of Artificial Intelligence',
    'future-of-artificial-intelligence',
    '<p>Artificial Intelligence is rapidly transforming our world. From healthcare to transportation, AI is revolutionizing every sector...</p><p>Machine learning algorithms are becoming more sophisticated, enabling computers to learn from data and make predictions with unprecedented accuracy.</p>',
    'Exploring how AI is shaping the future of technology and society.',
    3,
    1,
    'published'
),
(
    'Global Markets Show Strong Recovery',
    'global-markets-strong-recovery',
    '<p>Stock markets around the world have shown remarkable resilience in recent months. The Dow Jones, S&P 500, and international indices have all posted significant gains...</p>',
    'Analysis of the current state of global financial markets.',
    2,
    1,
    'published'
),
(
    'Championship Game Breaks Records',
    'championship-game-breaks-records',
    '<p>Last night\'s championship game was one for the history books. With over 100,000 fans in attendance and millions watching worldwide...</p>',
    'Historic championship game captivates global audience.',
    4,
    1,
    'published'
),
(
    'New Climate Agreement Signed',
    'new-climate-agreement-signed',
    '<p>World leaders have come together to sign a landmark climate agreement. The pact commits nations to reducing carbon emissions by 50% over the next decade...</p>',
    'Major international climate accord reached by world leaders.',
    6,
    1,
    'published'
);
