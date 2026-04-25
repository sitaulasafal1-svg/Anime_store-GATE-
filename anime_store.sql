-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 23, 2026 at 05:42 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `anime_store`
--

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `title`, `description`, `image`, `created_at`) VALUES
(2, 'Attack on Titan Final Season Ends, Closing a Historic Chapter in Anime', 'Entertainment Desk | Latest Update\r\n\r\nThe globally popular anime Attack on Titan has officially concluded its final season, bringing an end to a series that has defined modern anime for over a decade. Originally premiering in 2013 and adapted from Hajime Isayama’s manga, the show quickly gained international recognition for its intense storytelling and unexpected plot twists.\r\n\r\nThe final season focuses on protagonist Eren Yeager, whose dramatic transformation drives the story toward its conclusion. Moving beyond its early premise of humanity fighting Titans, the narrative shifts into a broader conflict involving war, ideology, and freedom. The ending explores the consequences of Eren’s choices, delivering a conclusion that has sparked strong reactions and discussions among fans worldwide.\r\n\r\nAnimated by MAPPA, the final installment maintained high production standards, featuring cinematic visuals and emotionally charged sequences. The concluding episodes resolve major mysteries surrounding the Titans and bring closure to key character arcs.\r\n\r\nAttack on Titan has been widely praised for its complex themes, including morality, sacrifice, and the cyclical nature of violence. Over the years, it has received multiple awards and played a significant role in expanding anime’s global audience.\r\n\r\nWith its conclusion, the series leaves behind a lasting legacy as one of the most impactful and influential anime of its generation.', '1776939375_1776674324_news (1).jpg', '2026-04-23 10:16:15'),
(3, 'A Silent Voice Continues to Resonate as a Landmark Anime Film on Bullying and Redemption', 'Entertainment Desk | Feature Update\r\n\r\nThe critically acclaimed anime film A Silent Voice remains one of the most impactful animated features addressing bullying, disability, and mental health, years after its initial release. Produced by Kyoto Animation and directed by Naoko Yamada, the film has continued to receive global recognition for its emotional storytelling and sensitive portrayal of human relationships.\r\n\r\nBased on Yoshitoki Ōima’s manga, the story follows Shoya Ishida, a former bully seeking redemption, and Shoko Nishimiya, a deaf girl who becomes the target of his actions during childhood. The narrative explores the long-term consequences of bullying, social isolation, and guilt, while emphasizing themes of forgiveness, self-acceptance, and personal growth.\r\n\r\nSince its debut, A Silent Voice has been praised for its realistic depiction of disability and communication barriers, along with its nuanced approach to mental health issues such as depression and anxiety. The film’s visual storytelling, combined with a powerful musical score, has helped it stand out as a deeply moving cinematic experience.\r\n\r\nOver time, the movie has gained a strong international audience and is frequently included in discussions about socially relevant anime. Critics and viewers alike have highlighted its importance in raising awareness about bullying and promoting empathy among younger audiences.\r\n\r\nAs conversations around mental health and inclusivity continue to grow worldwide, A Silent Voice remains a significant cultural work, reinforcing the role of animation as a medium capable of addressing serious and meaningful topics.', '1776939418_1776674493_news (2).jpg', '2026-04-23 10:16:58'),
(4, 'Frieren: Beyond Journey’s End Gains Global Acclaim for Its Emotional Storytelling', 'Entertainment Desk | Feature Update\r\n\r\nThe anime series Frieren: Beyond Journey’s End has emerged as one of the most critically acclaimed releases in recent years, captivating audiences with its unique narrative approach and emotional depth. Adapted from the manga by Kanehito Yamada and Tsukasa Abe, and produced by Madhouse, the series offers a refreshing take on the fantasy genre by focusing on life after the traditional “hero’s journey” has ended.\r\n\r\nUnlike conventional fantasy stories centered on battles and quests, Frieren follows the elven mage Frieren as she reflects on her past adventures with a group of heroes who have already defeated the Demon King. As an elf with an exceptionally long lifespan, Frieren begins to confront the fleeting nature of human life, leading to a deeply introspective journey about time, memory, and human connection.\r\n\r\nThe series has been widely praised for its शांत pacing, detailed world-building, and emotionally resonant storytelling. Its exploration of grief, friendship, and the passage of time has struck a chord with viewers, setting it apart from action-driven anime currently dominating the industry.\r\n\r\nMadhouse’s high-quality animation and carefully crafted visuals further enhance the storytelling, with scenic landscapes and subtle character expressions contributing to the show’s immersive atmosphere. Critics have also highlighted the series’ ability to balance quiet, reflective moments with meaningful character development.\r\n\r\nSince its release, Frieren: Beyond Journey’s End has steadily gained international popularity and critical recognition, positioning itself as a standout title in modern anime. As audiences continue to engage with its thoughtful narrative, the series is being recognized not just as a fantasy story, but as a profound exploration of life, loss, and what it truly means to cherish time.', '1776939520_1776674659_news (3).jpg', '2026-04-23 10:18:40'),
(5, 'Death Note Remains a Defining Psychological Thriller in Anime History', 'Entertainment Desk | Feature Update\r\n\r\nThe iconic anime Death Note continues to hold its status as one of the most influential psychological thrillers in the anime industry, years after its original release. Adapted from the manga by Tsugumi Ohba and Takeshi Obata, the series first aired in 2006 and quickly gained global recognition for its intense narrative and intellectual depth.\r\n\r\nThe story follows Light Yagami, a high-achieving student who discovers a mysterious notebook that grants him the power to kill anyone whose name he writes in it. As Light adopts the alias “Kira” and attempts to reshape the world according to his vision of justice, he is challenged by the enigmatic detective L, leading to a high-stakes battle of intellect and strategy.\r\n\r\nDeath Note is widely praised for its exploration of morality, justice, and the consequences of absolute power. The series presents a complex conflict where the line between right and wrong becomes increasingly blurred, encouraging viewers to question ethical boundaries and the nature of justice itself.\r\n\r\nThe anime’s gripping storytelling, combined with strong character development and suspenseful pacing, has contributed to its enduring popularity among global audiences. It has also played a key role in introducing many viewers to anime, becoming a gateway series for international fans.\r\n\r\nEven years after its release, Death Note remains culturally significant, frequently referenced in discussions about the greatest anime of all time. Its lasting impact highlights the power of storytelling that challenges viewers intellectually while delivering compelling entertainment.', '1776939581_1776674739_news (4).jpg', '2026-04-23 10:19:41'),
(6, 'Spy x Family Continues Global Popularity with Blend of Action, Comedy, and Heartwarming Storytelling', 'Entertainment Desk | Feature Update\r\n\r\nThe anime series Spy x Family continues to enjoy widespread global popularity, establishing itself as one of the most successful modern anime franchises. Adapted from Tatsuya Endo’s manga, the series has gained a massive following for its unique mix of espionage, comedy, and family-centered storytelling.\r\n\r\nThe story revolves around master spy Twilight, who must create a fake family to complete a high-stakes mission. Unbeknownst to him, his adopted daughter Anya possesses telepathic abilities, while his wife Yor leads a secret life as an assassin. This unusual setup creates a blend of humor, action, and emotional moments that have resonated strongly with audiences worldwide.\r\n\r\nSince its anime adaptation, Spy x Family has been praised for its engaging characters, lighthearted tone, and high-quality animation produced by Wit Studio and CloverWorks. The series stands out for balancing intense spy-themed missions with wholesome family interactions, making it accessible to a wide range of viewers.\r\n\r\nThe character of Anya, in particular, has become a cultural icon, contributing significantly to the show’s viral popularity across social media platforms. The franchise has also seen strong manga sales and continued expansion through new seasons and related content.\r\n\r\nAs the series continues to release new episodes and story arcs, Spy x Family remains a major force in the anime industry, reflecting the growing global demand for diverse and family-friendly animated storytelling.', '1776939640_1776674877_news (5).jpg', '2026-04-23 10:20:40'),
(7, 'Jujutsu Kaisen Marks Major Milestones, Strengthening Its Position in Modern Anime', 'Entertainment Desk | Feature Update\r\n\r\nThe popular anime and manga series Jujutsu Kaisen continues to solidify its status as one of the leading titles in contemporary anime, following significant milestones in both its manga serialization and anime adaptation. Created by Gege Akutami and serialized in Weekly Shonen Jump, the series has seen rapid growth in global popularity since its debut.\r\n\r\nThe story follows Yuji Itadori, a high school student who becomes involved in the world of curses after ingesting a powerful cursed object. As he joins the Tokyo Jujutsu High, the narrative unfolds through intense battles, supernatural elements, and evolving character dynamics. The series is widely recognized for its fast-paced storytelling, well-choreographed action sequences, and dark thematic undertones.\r\n\r\nRecent developments, including major story arcs and anime releases, have further elevated the franchise’s global reach. The anime adaptation, produced by MAPPA, has been praised for its high-quality animation and dynamic fight scenes, contributing to its strong reception among audiences and critics alike.\r\n\r\nJujutsu Kaisen has also achieved impressive commercial success, with manga sales surging worldwide and consistent rankings among top-selling titles. Its presence on major magazine covers and anniversary milestones highlights its growing influence within the industry.\r\n\r\nAs the series continues to expand through new arcs and adaptations, Jujutsu Kaisen remains a dominant force in anime and manga, reflecting the evolving trends and global demand for action-driven storytelling.', '1776939769_1776674964_news (6).jpg', '2026-04-23 10:22:49'),
(8, 'Attack on Titan Continues to Dominate Global Anime Rankings with Lasting Impact', 'Entertainment Desk | Feature Update\r\n\r\nThe globally renowned anime Attack on Titan continues to maintain its strong influence in the anime industry, even after the conclusion of its final season. Originally adapted from Hajime Isayama’s manga, the series has remained a benchmark for storytelling, animation quality, and global reach since its debut in 2013.\r\n\r\nSet in a world threatened by giant humanoid creatures known as Titans, the story follows Eren Yeager and humanity’s fight for survival within fortified walls. Over time, the narrative evolved into a complex exploration of war, freedom, and moral conflict, distinguishing itself from traditional action-driven anime.\r\n\r\nThe series’ final chapters, produced by MAPPA, delivered a cinematic conclusion that sparked widespread discussion among fans and critics. Its intense visuals, large-scale action sequences, and emotionally charged storytelling have been widely praised as some of the most impactful moments in modern anime.\r\n\r\nAttack on Titan has consistently ranked among the top anime worldwide, with strong streaming numbers and a dedicated international fanbase. Its success has contributed significantly to the global expansion of anime, attracting new audiences and setting higher standards for narrative depth.\r\n\r\nEven after its conclusion, the franchise continues to trend across platforms, with ongoing discussions, fan theories, and retrospective analyses highlighting its cultural and artistic significance.\r\n\r\nAs one of the defining anime of its generation, Attack on Titan leaves behind a legacy that continues to shape the future of the industry.', '1776939818_1776675185_news (7).jpg', '2026-04-23 10:23:38'),
(9, 'One Punch Man Maintains Global Popularity with Its Unique Take on Superhero Genre', 'Entertainment Desk | Feature Update\r\n\r\nThe anime series One Punch Man continues to hold a strong position in global pop culture, recognized for its unconventional approach to the superhero genre. Originally created by ONE as a webcomic and later illustrated by Yusuke Murata, the series has gained widespread acclaim for blending action, satire, and comedy.\r\n\r\nThe story centers on Saitama, a hero who can defeat any opponent with a single punch, leading to an unexpected twist on traditional hero narratives. Despite his overwhelming strength, Saitama struggles with boredom and a lack of recognition, offering a humorous yet reflective take on power and purpose.\r\n\r\nSince its anime debut, One Punch Man has been praised for its high-quality animation—particularly in its first season—and its ability to parody common tropes found in both superhero and anime genres. The series stands out for balancing intense fight sequences with comedic timing and character-driven storytelling.\r\n\r\nThe franchise has maintained strong global engagement through manga releases, streaming platforms, and ongoing discussions about future anime seasons. Fans continue to anticipate new developments, particularly regarding upcoming story arcs and potential adaptations.\r\n\r\nAs superhero narratives remain popular worldwide, One Punch Man distinguishes itself by offering a fresh perspective, reinforcing its status as one of the most innovative and entertaining anime series of its time.', '1776939863_1776675295_news (8).jpg', '2026-04-23 10:24:23');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `transaction_uuid` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_status` varchar(50) DEFAULT 'pending',
  `ref_id` varchar(255) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_price`, `status`, `transaction_uuid`, `created_at`, `payment_status`, `ref_id`, `payment_method`) VALUES
(1, 4, 2000.00, 'paid', 'ORD-69e9f4629263a', '2026-04-23 10:28:50', 'Paid', '000F07S', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `price`, `quantity`) VALUES
(1, 1, 1, 2000.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `category`, `description`, `price`, `image`, `created_at`) VALUES
(1, 'killua model', 'Action Figures', 'A collectible figure of Killua Zoldyck from Hunter x Hunter captures his electrifying abilities and intense character presence, highlighting the series’ action-driven appeal and enduring fan popularity.', 2000.00, '1776940091_0.jpg', '2026-04-23 10:28:11'),
(2, 'Naruto', 'Action Figures', 'This action figure captures a dynamic character pose with detailed craftsmanship, highlighting signature abilities, expressive design, and high-quality sculpting—making it a standout collectible for fans and display enthusiasts alike.', 3000.00, '1776940861_1.jpg', '2026-04-23 10:41:01');

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `testimonials`
--

INSERT INTO `testimonials` (`id`, `user_id`, `name`, `message`, `created_at`) VALUES
(1, 4, 'Kira', 'good products', '2026-04-23 10:08:33');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','customer') DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`, `profile_image`) VALUES
(1, 'admin', 'admin@gmail.com', '$2y$10$hQzZ61S6jcadhTybyeSp2.sGPjzMDV/ZWVs1SJ3L0Ui6u/Wuyw4bS', 'admin', '2026-04-23 09:54:52', NULL),
(4, 'Kira', 'kira1@gmail.com', '$2y$10$SA50EDD1po6cKPeB5dxWnuepyyDo2uY0bXvMTAEgPAD1mftB7lc2y', 'customer', '2026-04-23 10:05:37', 'uploads/profile/1776938849_1766995573_✗ ¦ ↱𝑨𝒊𝒛𝒆𝒏↲ ¦ ✗.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`user_id`,`product_id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
