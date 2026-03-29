-- Listeners Lounge Database
-- INT1059 Advanced Web - Chonlatorn Inthayat (Student ID: 14929)

CREATE DATABASE IF NOT EXISTS listeners_lounge CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE listeners_lounge;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Albums table
CREATE TABLE IF NOT EXISTS albums (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    artist VARCHAR(100) NOT NULL,
    release_year YEAR NOT NULL,
    genre VARCHAR(50) NOT NULL,
    cover_color VARCHAR(20) DEFAULT '#1a1a2e',
    cover_emoji VARCHAR(10) DEFAULT '🎵',
    track_listing TEXT,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Reviews table
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    album_id INT NOT NULL,
    rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    review_text TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (album_id) REFERENCES albums(id) ON DELETE CASCADE,
    UNIQUE KEY unique_review (user_id, album_id)
);

-- Favourites table
CREATE TABLE IF NOT EXISTS favourites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    album_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (album_id) REFERENCES albums(id) ON DELETE CASCADE,
    UNIQUE KEY unique_favourite (user_id, album_id)
);

-- Seed albums data (25 albums across genres)
INSERT INTO albums (title, artist, release_year, genre, cover_color, cover_emoji, track_listing, description) VALUES
('Currents', 'Tame Impala', 2015, 'Indie', '#3d1a6e', '🌊', 'Let It Happen, Nangs, The Moment, Yes I''m Changing, Eventually, Gossip, The Less I Know The Better, Past Life, Disciples, ''Cause I''m a Man, Reality in Motion, Love/Paranoia, New Person Same Old Mistakes', 'A groundbreaking psychedelic pop album that marked Kevin Parker''s bold shift from guitar-driven rock to synth-laden, introspective songwriting. Currents explores themes of change, love, and personal transformation.'),
('Morning Glory', 'Oasis', 1995, 'Rock', '#1a3a5c', '☀️', 'Hello, Roll With It, Wonderwall, Don''t Look Back in Anger, Hey Now!, The Masterplan, Some Might Say, Cast No Shadow, She''s Electric, Morning Glory, Champagne Supernova', 'The definitive Britpop landmark. Oasis delivered an album of anthemic, stadium-ready rock that defined a generation. With Wonderwall and Don''t Look Back in Anger, it remains one of the best-selling British albums of all time.'),
('Starboy', 'The Weeknd', 2016, 'R&B', '#2d0a4a', '⭐', 'Starboy, Party Monster, False Alarm, Reminder, Rockin, Secrets, True Colors, Snowchild, Tie My Hands, A Lonely Night, Attention, I Feel It Coming, Die For You, All I Know', 'A sleek, dark R&B journey through fame, excess, and vulnerability. The Weeknd crafts cinematic soundscapes that blur the line between pop ambition and underground cool.'),
('21', 'Adele', 2011, 'Pop', '#5c3317', '💔', 'Rolling in the Deep, Rumour Has It, Turning Tables, Don''t You Remember, Set Fire to the Rain, He Won''t Go, Take It All, I''ll Be Waiting, One and Only, Lovesong, Someone Like You', 'One of the best-selling albums of all time. Adele''s raw, powerful vocals carry emotionally devastating songs about heartbreak and moving on. A modern soul classic.'),
('Divide', 'Ed Sheeran', 2017, 'Pop', '#8a2a2a', '➗', 'Eraser, Castle on the Hill, Dive, Shape of You, Perfect, Galway Girl, Happier, New Man, Hearts Don''t Break Around Here, What Do I Know, How Would You Feel, Supermarket Flowers, Barcelona, Bibia Be Ye Ye, Nancy Mulligan, Save Myself', 'Ed Sheeran''s third studio album showcases his incredible versatility — from intimate ballads to dancehall-influenced pop. A deeply personal record that became a global phenomenon.'),
('Summer Days', 'The Beach Boys', 1965, 'Rock', '#1a5c8a', '🏖️', 'The Girl from New York City, Amusement Parks U.S.A., Then I Kissed Her, Salt Lake City, Girl Don''t Tell Me, Help Me Ronda, California Girls, Let Him Run Wild, You''re So Good to Me', 'A sun-soaked snapshot of 1960s California. The Beach Boys perfected their lush harmonies and carefree spirit on this classic summer record full of timeless hits.'),
('To Pimp a Butterfly', 'Kendrick Lamar', 2015, 'Hip-Hop', '#1a3a1a', '🦋', 'Wesley''s Theory, For Free?, King Kunta, Institutionalized, These Walls, u, Alright, For Sale?, Momma, Hood Politics, How Much a Dollar Cost, Complexion, The Blacker the Berry, You Ain''t Gotta Lie, i, Mortal Man', 'A towering achievement in hip-hop. Kendrick weaves jazz, funk, and spoken word into a devastating meditation on Black identity, systemic racism, and self-worth. Widely regarded as a masterpiece.'),
('Random Access Memories', 'Daft Punk', 2013, 'Electronic', '#8a6a1a', '🤖', 'Give Life Back to Music, The Game of Love, Giorgio by Moroder, Within, Instant Crush, Lose Yourself to Dance, Touch, Get Lucky, Beyond, Motherboard, Fragments of Time, Doin'' It Right, Contact', 'A love letter to the golden era of disco and funk. Daft Punk assembled legendary musicians to craft an analogue masterpiece that transcends genres and eras.'),
('Blue', 'Joni Mitchell', 1971, 'Indie', '#2a5c8a', '💙', 'All I Want, My Old Man, Little Green, Carey, Blue, California, This Flight Tonight, River, A Case of You, The Last Time I Saw Richard', 'Perhaps the most intimate album ever recorded. Joni Mitchell strips everything back to voice and acoustic guitar, revealing confessional poetry of extraordinary emotional depth.'),
('In Rainbows', 'Radiohead', 2007, 'Rock', '#3a1a5c', '🌈', '15 Step, Bodysnatchers, Nude, Weird Fishes/Arpeggi, All I Need, Faust Arp, Reckoner, House of Cards, Jigsaw Falling into Place, Videotape', 'Radiohead''s warmest, most human record. After years of electronic experimentation, they returned to organic textures and melodic beauty. A haunting, gorgeous achievement.'),
('good kid m.A.A.d city', 'Kendrick Lamar', 2012, 'Hip-Hop', '#2a1a3a', '🎯', 'Sherane, Bitch Don''t Kill My Vibe, Backseat Freestyle, The Art of Peer Pressure, Money Trees, Poetic Justice, good kid, m.A.A.d city, Swimming Pools, Sing About Me, Real, Compton', 'A cinematic coming-of-age story set in Compton. Kendrick''s debut major-label album plays like a short film — deeply narrative, musically adventurous, and emotionally resonant.'),
('folklore', 'Taylor Swift', 2020, 'Indie', '#4a4a4a', '🌲', 'the 1, cardigan, the last great american dynasty, exile, my tears ricochet, mirrorball, seven, august, this is me trying, illicit affairs, invisible string, mad woman, epiphany, betty, peace, hoax', 'Taylor Swift''s unexpected pivot to indie folk. Produced with Aaron Dessner and Justin Vernon, folklore is atmospheric, literary, and melancholic — her most artistically cohesive work.'),
('After Hours', 'The Weeknd', 2020, 'R&B', '#1a0a2a', '🌙', 'Alone Again, Too Late, Hardest to Love, Scared to Live, Snowchild, Escape from LA, Heartless, Faith, Blinding Lights, In Your Eyes, Save Your Tears, Repeat After Me, After Hours', 'A haunting concept album about loneliness, fame, and self-destruction. The Weeknd channels 80s synth-pop through a contemporary lens, creating his most cohesive and cinematic record.'),
('Kind of Blue', 'Miles Davis', 1959, 'Jazz', '#1a2a3a', '🎺', 'So What, Freddie Freeloader, Blue in Green, All Blues, Flamenco Sketches', 'The best-selling jazz album of all time and a defining moment in modal jazz. Miles Davis assembled an all-star ensemble to create music that feels like pure improvised perfection.'),
('A Love Supreme', 'John Coltrane', 1965, 'Jazz', '#3a2a1a', '🎷', 'Acknowledgement, Resolution, Pursuance, Psalm', 'John Coltrane''s spiritual masterpiece. This four-part suite represents the pinnacle of jazz expressionism — a deeply personal offering of gratitude and transcendence.'),
('The Dark Side of the Moon', 'Pink Floyd', 1973, 'Rock', '#0a0a1a', '🌑', 'Speak to Me, Breathe, On the Run, Time, The Great Gig in the Sky, Money, Us and Them, Any Colour You Like, Brain Damage, Eclipse', 'One of the greatest albums ever made. Pink Floyd created a seamless, thematic journey through anxiety, greed, mental illness, and mortality. Its influence cannot be overstated.'),
('Lemonade', 'Beyoncé', 2016, 'R&B', '#8a6a1a', '🍋', 'Pray You Catch Me, Hold Up, Don''t Hurt Yourself, Sorry, 6 Inch, Daddy Lessons, Love Drought, Sandcastles, Forward, Freedom, All Night, Formation', 'A visual album of extraordinary ambition. Beyoncé explores infidelity, forgiveness, Black womanhood, and resilience through a genre-defying blend of R&B, rock, country, and trap.'),
('Blonde', 'Frank Ocean', 2016, 'R&B', '#8a7a3a', '🌸', 'Nikes, Ivy, Pink + White, Be Yourself, Solo, Skyline To, Self Control, Good Guy, Nights, Solo (Reprise), Pretty Sweet, Facebook Story, Close to You, White Ferrari, Seigfried, Godspeed, Futura Free', 'Frank Ocean''s long-awaited masterpiece. Blonde is intimate, fragmented, and deeply personal — exploring memory, sexuality, and longing through unconventional song structures.'),
('Rumours', 'Fleetwood Mac', 1977, 'Rock', '#5c4a3a', '💨', 'Second Hand News, Dreams, Never Going to Give You Up, Don''t Stop, Go Your Own Way, The Chain, You Make Loving Fun, I Don''t Want to Know, Oh Daddy, Gold Dust Woman', 'Recorded while the band was falling apart, Rumours channels real heartbreak into perfect pop songcraft. Every track is a classic, and together they form an unassailable whole.'),
('Channel Orange', 'Frank Ocean', 2012, 'R&B', '#8a4a1a', '🍊', 'Start, Thinkin Bout You, Fertilizer, Sierra Leone, Sweet Life, Not Just Money, Super Rich Kids, Pilot Jones, Crack Rock, Pyramids, Lost, White, Monks, Bad Religion, Pink Matter, Forrest Gump, End', 'Frank Ocean''s debut album announced one of his generation''s most original voices. Channel Orange is lush, cinematic, and emotionally complex — exploring class, desire, and nostalgia.'),
('1989', 'Taylor Swift', 2014, 'Pop', '#6a8a9a', '📷', 'Welcome to New York, Blank Space, Style, Out of the Woods, All You Had to Do Was Stay, Shake It Off, I Wish You Would, Bad Blood, Wildest Dreams, How You Get the Girl, This Love, Clean, New Romantics', 'Taylor Swift''s full pop reinvention. 1989 is a sleek, hook-laden record that cements her as a genuine pop auteur. Massive in scale but surprisingly emotional in its depths.'),
('Nevermind', 'Nirvana', 1991, 'Rock', '#3a5a7a', '👶', 'Smells Like Teen Spirit, In Bloom, Come as You Are, Breed, Lithium, Polly, Territorial Pissings, Drain You, Lounge Act, Stay Away, On a Plain, Something in the Way', 'The album that changed rock music forever. Nevermind''s explosive blend of punk aggression and pop melody gave voice to a disaffected generation and launched grunge into the mainstream.'),
('Melodrama', 'Lorde', 2017, 'Indie', '#5a1a6a', '🎭', 'Green Light, Sober, Homemade Dynamite, The Louvre, Liability, Hard Feelings/Loveless, Sober II, Writer in the Dark, Supercut, Liability (Reprise), Perfect Places', 'A coming-of-age album of rare emotional intelligence. Lorde documents the euphoria and devastation of young adult parties and heartbreak with the precision of a seasoned novelist.'),
('Coloring Book', 'Chance the Rapper', 2016, 'Hip-Hop', '#e87a1a', '🎨', 'All We Got, No Problem, Summer Friends, D.R.A.M. Sings Special, Blessings, Same Drugs, Angels, Juke Jam, All Night, How Great, Smoke Break, Finish Line/Drown, Blessings (Reprise)', 'A joyful gospel-rap mixtape that radiates positivity and faith. Chance the Rapper delivers his most personal and musically ambitious work, blending Chicago soul with contemporary hip-hop.'),
('No.6 Collaborations Project', 'Ed Sheeran', 2019, 'Pop', '#2a3a5c', '🤝', 'Beautiful People, South of the Border, Cross Me, Best Part of Me, End Game, Heavenly, Antisocial, Remember the Name, Feels, Put It All on Me, Nothing on You, I Don''t Care, Blow, Take Me Back to London, Way to Break My Heart', 'Ed Sheeran teams up with a who''s who of contemporary music. A collaborative project that showcases his chameleonic ability to inhabit different genres and styles with ease.'),
('This Is Acting', 'Sia', 2016, 'Pop', '#8a1a5c', '🎭', 'Bird Set Free, Alive, One Million Bullets, Cheap Thrills, Move Your Body, Unstoppable, Reaper, House on Fire, Footprints, Sweet Design, Broken Glass, Freeze You Out, Space Between', 'Sia''s most commercially successful album. Each song was originally written for another artist, yet they cohere into a powerful statement about resilience, vulnerability, and reinvention.');

-- Sample users (passwords are hashed versions of "password123")
INSERT INTO users (username, email, password) VALUES
('MusicLover42', 'musiclover@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('JazzTeacher42', 'jazz@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('IndieFan99', 'indie@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Sample reviews
INSERT INTO reviews (user_id, album_id, rating, review_text) VALUES
(1, 1, 5, 'Currents completely changed how I think about music. The production is so layered and intricate — every listen reveals something new. The Less I Know The Better is an absolute banger.'),
(2, 14, 5, 'Kind of Blue is where jazz begins and ends. Every musician on this record was at the absolute peak of their craft. So What alone justifies the album''s legendary status.'),
(1, 7, 5, 'To Pimp a Butterfly is the most important rap album of the decade. Alright became an anthem for a reason. Kendrick is operating on a completely different level.'),
(3, 12, 4, 'folklore surprised me with how cohesive it feels. Taylor finally found her artistic voice here. cardigan and august are stunning.'),
(2, 1, 4, 'Incredible record. Parker''s production is genre-defying. Eventually and New Person Same Old Mistakes are standouts.'),
(1, 16, 5, 'Dark Side of the Moon is not just an album — it''s an experience. The way each track flows into the next is perfection.'),
(3, 4, 5, 'Adele''s voice on this album is otherworldly. Someone Like You had me in tears the first time I heard it.');

-- Sample favourites
INSERT INTO favourites (user_id, album_id) VALUES
(1, 1), (1, 7), (1, 16), (1, 4),
(2, 14), (2, 15), (2, 1),
(3, 12), (3, 13), (3, 22);
