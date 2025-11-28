-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 28, 2025 at 01:28 PM
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
-- Database: `absensi`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--
-- Error reading structure for table absensi.sessions: #1932 - Table &#039;absensi.sessions&#039; doesn&#039;t exist in engine
-- Error reading data for table absensi.sessions: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near &#039;FROM `absensi`.`sessions`&#039; at line 1

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `jabatan` varchar(100) DEFAULT NULL,
  `bidang` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `username`, `password`, `jabatan`, `bidang`) VALUES
(1, 'admin', 'Admin', 'fdcd241b81024ffc7307d10e933580ca', 'ADMIN', 'SEKRETARIAT'),
(2, 'fathimatuzzahra', 'fathimatuzzahra', 'fdcd241b81024ffc7307d10e933580ca', 'plt. kepala dinas', 'SEKRETARIAT'),
(3, 'noor eka hasni', 'noorekahasni', 'fdcd241b81024ffc7307d10e933580ca', 'kepala sub bagian umum dan kepegawaian', 'SEKRETARIAT'),
(4, 'yuli istiarini', 'yuliistiarini', 'fdcd241b81024ffc7307d10e933580ca', 'kepala sub bagian perencanaan dan pelaporan', 'SEKRETARIAT'),
(5, 'm. rachman hidayat', 'mrachmanhidayat', 'fdcd241b81024ffc7307d10e933580ca', 'kepala sub bagian keuangan dan asset', 'SEKRETARIAT'),
(6, 'rahmah', 'rahmah', 'fdcd241b81024ffc7307d10e933580ca', 'penelaah teknis kebijakan', 'SEKRETARIAT'),
(7, 'rina wardani', 'rinawardani', 'fdcd241b81024ffc7307d10e933580ca', 'penelaah teknis kebijakan', 'SEKRETARIAT'),
(8, 'linda lidiana', 'lindalidiana', 'fdcd241b81024ffc7307d10e933580ca', 'penelaah teknis kebijakan', 'SEKRETARIAT'),
(9, 'abdul raji', 'abdulraji', 'fdcd241b81024ffc7307d10e933580ca', 'pengadministrasi perkantoran', 'SEKRETARIAT'),
(10, 'muhammad anas', 'muhammadanas', 'fdcd241b81024ffc7307d10e933580ca', 'pengadministrasi perkantoran', 'SEKRETARIAT'),
(11, 'sinar octaviani', 'sinaroctaviani', 'fdcd241b81024ffc7307d10e933580ca', 'penelaah teknis kebijakan', 'SEKRETARIAT'),
(12, 'hafsah melly farilah', 'hafsahmellyfarilah', 'fdcd241b81024ffc7307d10e933580ca', 'pengolah data dan informasi', 'SEKRETARIAT'),
(13, 'ali sulaimansyah', 'alisulaimansyah', 'fdcd241b81024ffc7307d10e933580ca', 'pengelola umum operasional', 'SEKRETARIAT'),
(14, 'adhitya ari dwi cahyo', 'adhityaaridwicahyo', 'fdcd241b81024ffc7307d10e933580ca', 'arsiparis terampil', 'SEKRETARIAT'),
(15, 'eka norlena dewi', 'ekanorlenadewi', 'fdcd241b81024ffc7307d10e933580ca', 'pranata komputer ahli pertama', 'SEKRETARIAT'),
(16, 'mira febriani', 'mirafebriani', 'fdcd241b81024ffc7307d10e933580ca', 'penata layanan operasional', 'SEKRETARIAT'),
(17, 'muhammad rizwan', 'muhammadrizwan', 'fdcd241b81024ffc7307d10e933580ca', 'pengelola layanan operasional', 'SEKRETARIAT'),
(18, 'mitha sari rachmayanti', 'mithasarirachmayanti', 'fdcd241b81024ffc7307d10e933580ca', 'petugas layanan operasional', 'SEKRETARIAT'),
(19, 'haekal aqla almaraghi', 'haekalaqlaalmaraghi', 'fdcd241b81024ffc7307d10e933580ca', 'petugas layanan operasional', 'SEKRETARIAT'),
(20, 'muhammad nabil nurdea', 'muhammadnabilnurdea', 'fdcd241b81024ffc7307d10e933580ca', 'petugas layanan operasional', 'SEKRETARIAT'),
(21, 'muhammad wisnu karyadi', 'muhammadwisnukaryadi', 'fdcd241b81024ffc7307d10e933580ca', 'petugas layanan operasional', 'SEKRETARIAT'),
(22, 'muhammad ulyani', 'muhammadulyani', 'fdcd241b81024ffc7307d10e933580ca', 'petugas layanan operasional', 'SEKRETARIAT'),
(23, 'akhmad fauzi', 'akhmadfauzi', 'fdcd241b81024ffc7307d10e933580ca', 'petugas layanan operasional', 'SEKRETARIAT'),
(24, 'muhammad sapuani', 'muhammadsapuani', 'fdcd241b81024ffc7307d10e933580ca', 'petugas layanan operasional phl', 'SEKRETARIAT'),
(25, 'sandy wijaya', 'sandywijaya', 'fdcd241b81024ffc7307d10e933580ca', 'petugas layanan operasional', 'SEKRETARIAT'),
(26, 'rolian noor', 'roliannoor', 'fdcd241b81024ffc7307d10e933580ca', 'petugas layanan operasional', 'SEKRETARIAT'),
(27, 'rudiyanoor', 'rudiyanoor', 'fdcd241b81024ffc7307d10e933580ca', 'petugas layanan operasional', 'SEKRETARIAT'),
(28, 'abdul latif', 'abdullatif', 'fdcd241b81024ffc7307d10e933580ca', 'petugas layanan operasional', 'SEKRETARIAT'),
(29, 'iqbal priyangga', 'iqbalpriyangga', 'fdcd241b81024ffc7307d10e933580ca', 'petugas layanan operasional', 'SEKRETARIAT'),
(30, 'mahyuda', 'mahyuda', 'fdcd241b81024ffc7307d10e933580ca', 'petugas layanan operasional', 'SEKRETARIAT'),
(31, 'mashur', 'mashur', 'fdcd241b81024ffc7307d10e933580ca', 'petugas layanan operasional', 'SEKRETARIAT'),
(32, 'khairun naim', 'khairunnaim', 'fdcd241b81024ffc7307d10e933580ca', 'petugas layanan operasional', 'SEKRETARIAT'),
(33, 'paisal riza', 'paisalriza', 'fdcd241b81024ffc7307d10e933580ca', 'petugas layanan operasional', 'SEKRETARIAT'),
(34, 'ichwan syarif', 'ichwansyarif', 'fdcd241b81024ffc7307d10e933580ca', 'petugas layanan operasional', 'SEKRETARIAT'),
(35, 'susi rosana', 'susirosana', 'fdcd241b81024ffc7307d10e933580ca', 'petugas layanan operasional', 'SEKRETARIAT'),
(36, 'much nur cahyadi', 'muchnurcahyadi', 'fdcd241b81024ffc7307d10e933580ca', 'petugas layanan operasional', 'SEKRETARIAT'),
(37, 'mahrida', 'mahrida', 'fdcd241b81024ffc7307d10e933580ca', 'petugas layanan operasional', 'SEKRETARIAT'),
(38, 'sugiono', 'sugiono', 'fdcd241b81024ffc7307d10e933580ca', 'petugas layanan operasional', 'SEKRETARIAT'),
(39, 'emmy ariani', 'emmyariani', 'fdcd241b81024ffc7307d10e933580ca', 'kepala bidang pengendalian pencemaran dan kerusakan lh', 'PPKLH'),
(40, 'hartopo', 'hartopo', 'fdcd241b81024ffc7307d10e933580ca', 'kepala seksi pemulihan pencemaran dan kerusakan lh', 'PPKLH'),
(41, 'yuliarini', 'yuliarini', 'fdcd241b81024ffc7307d10e933580ca', 'kepala seksi pencegahan pencemaran dan kerusakan lh', 'PPKLH'),
(42, 'lalu erwin suprayanto', 'lalurewinsuprayanto', 'fdcd241b81024ffc7307d10e933580ca', 'kepala seksi pengelolan sampah limbah dan b3', 'PPKLH'),
(43, 'rahmaniansyah', 'rahmaniansyah', 'fdcd241b81024ffc7307d10e933580ca', 'pengadministrasi perkantoran', 'PPKLH'),
(44, 'adi rizkian noor', 'adirizkiannoor', 'fdcd241b81024ffc7307d10e933580ca', 'pengendali dampak lingkungan ahli pertama', 'PPKLH'),
(45, 'muhammad rizky noor pratama', 'muhammadrizkynoorpratama', 'fdcd241b81024ffc7307d10e933580ca', 'pengendali dampak lingkungan ahli pertama', 'PPKLH'),
(46, 'maimunah', 'maimunah', 'fdcd241b81024ffc7307d10e933580ca', 'petugas layanan operasional', 'PPKLH'),
(47, 'octaviana dewi syahputri', 'octavianadewisyahputri', 'fdcd241b81024ffc7307d10e933580ca', 'petugas layanan operasional', 'PPKLH'),
(48, 'lilis maryani', 'lilismaryani', 'fdcd241b81024ffc7307d10e933580ca', 'petugas layanan operasional', 'PPKLH'),
(49, 'shonu dwi prayogo', 'shonudwiprayogo', 'fdcd241b81024ffc7307d10e933580ca', 'petugas layanan operasional', 'PPKLH'),
(50, 'fitria handayani', 'fitriahandayani', 'fdcd241b81024ffc7307d10e933580ca', 'petugas layanan operasional', 'PPKLH'),
(51, 'handriansyah s.ak', 'handriansyahsak', 'fdcd241b81024ffc7307d10e933580ca', 'petugas layanan operasional', 'PPKLH'),
(52, 'akhmad suhadi', 'akhmadsuhadi', 'fdcd241b81024ffc7307d10e933580ca', 'tenaga ahli', 'PPKLH'),
(53, 'muhammad indera wijaya', 'muhammadinderawijaya', 'fdcd241b81024ffc7307d10e933580ca', 'tenaga ahli', 'PPKLH'),
(54, 'hajie hariyanie', 'hajiehariyanie', 'fdcd241b81024ffc7307d10e933580ca', 'kepala seksi kemitraan', 'KPPI'),
(55, 'zainullah', 'zainullah', 'fdcd241b81024ffc7307d10e933580ca', 'kepala seksi konservasi dan keanekaragaman hayati', 'KPPI'),
(56, 'yudhi syarif', 'yudhisyarif', 'fdcd241b81024ffc7307d10e933580ca', 'kepala seksi pengendalian perubahan iklim', 'KPPI'),
(57, 'muhammad zamroni', 'muhammadzamroni', 'fdcd241b81024ffc7307d10e933580ca', 'penelaah teknis kebijakan', 'KPPI'),
(58, 'fathul umar aditya', 'fathulumaraditya', 'fdcd241b81024ffc7307d10e933580ca', 'penyuluh lingkungan hidup ahli pertama', 'KPPI'),
(59, 'marissa puji rosmawati', 'marissapujirosmawati', 'fdcd241b81024ffc7307d10e933580ca', 'pengadministrasi perkantoran', 'KPPI'),
(60, 'fazri noor hidayat', 'fazrinoorhidayat', 'fdcd241b81024ffc7307d10e933580ca', 'pengendali dampak lingkungan ahli muda', 'KPPI'),
(61, 'muhammad faisal', 'muhammadfaisal', 'fdcd241b81024ffc7307d10e933580ca', 'petugas layanan operasional', 'KPPI'),
(62, 'muhammad dhiyaul auliya', 'muhammaddhiyaulauliya', 'fdcd241b81024ffc7307d10e933580ca', 'petugas layanan operasional', 'KPPI'),
(63, 'helda febriani', 'heldafebriani', 'fdcd241b81024ffc7307d10e933580ca', 'petugas layanan operasional', 'KPPI'),
(64, 'norbaiti', 'norbaiti', 'fdcd241b81024ffc7307d10e933580ca', 'tenaga ahli', 'KPPI'),
(65, 'kholifah fiana rini', 'kholifahfianarini', 'fdcd241b81024ffc7307d10e933580ca', 'petugas layanan operasional', 'KPPI'),
(66, 'aprianor teguh saputra', 'aprianorteguhsaputra', 'fdcd241b81024ffc7307d10e933580ca', 'tenaga ahli', 'KPPI'),
(67, 'hardini wijayanti', 'hardiniwijayanti', 'fdcd241b81024ffc7307d10e933580ca', 'kepala bidang penaatan hukum lingkungan', 'PHL'),
(68, 'muhammad darma tri saputra', 'muhammaddarmatrisaputra', 'fdcd241b81024ffc7307d10e933580ca', 'kepala seksi pengaduan kasus lh dan penegakan hukum', 'PHL'),
(69, 'achmad yanuar', 'achmadyanuar', 'fdcd241b81024ffc7307d10e933580ca', 'kepala seksi pembinaan dan pengawasan lh', 'PHL'),
(70, 'nina tresnawati', 'ninatresnawati', 'fdcd241b81024ffc7307d10e933580ca', 'penelaah teknis kebijakan', 'PHL'),
(71, 'junindra jaya', 'junindrajaya', 'fdcd241b81024ffc7307d10e933580ca', 'pengolah data', 'PHL'),
(72, 'sofian rifani', 'sofianrifani', 'fdcd241b81024ffc7307d10e933580ca', 'penelaah teknis kebijakan', 'PHL'),
(73, 'shela rizkita dewi', 'shelarizkitadewi', 'fdcd241b81024ffc7307d10e933580ca', 'pengendali dampak lingkungan ahli pertama', 'PHL'),
(74, 'alya qatrunnada', 'alyaqatrunnada', 'fdcd241b81024ffc7307d10e933580ca', 'petugas layanan operasional', 'PHL'),
(75, 'fariz ramadhan', 'farizramadhan', 'fdcd241b81024ffc7307d10e933580ca', 'petugas layanan operasional', 'PHL'),
(76, 'muhammad nizham haitami', 'muhammadnizhamhaitami', 'fdcd241b81024ffc7307d10e933580ca', 'petugas layanan operasional', 'PHL'),
(77, 'nadhya maherwanda', 'nadhyamaherwanda', 'fdcd241b81024ffc7307d10e933580ca', 'petugas layanan operasional', 'PHL'),
(78, 'halimatus sadiah', 'halimatussadiah', 'fdcd241b81024ffc7307d10e933580ca', 'petugas layanan operasional', 'TALING'),
(79, 'rezeki nilam sari', 'rezekinilamsari', 'fdcd241b81024ffc7307d10e933580ca', 'petugas layanan operasional', 'TALING'),
(80, 'adhi maulana', 'adhimaulana', 'fdcd241b81024ffc7307d10e933580ca', 'kepala bidang tata lingkungan', 'TALING'),
(81, 'arif wardani', 'arifwardani', 'fdcd241b81024ffc7307d10e933580ca', 'kepala seksi perencanaan lh', 'TALING'),
(82, 'm.saleh', 'msaleh', 'fdcd241b81024ffc7307d10e933580ca', 'kepala seksi pengelolaan dampak lh', 'TALING'),
(83, 'yenny eranova', 'yennyeranova', 'fdcd241b81024ffc7307d10e933580ca', 'kepala seksi pengelolaan resiko kebijakan strategis', 'TALING'),
(84, 'aliah', 'aliah', 'fdcd241b81024ffc7307d10e933580ca', 'penelaah teknis kebijakan', 'TALING'),
(85, 'risa atika dewi', 'risatikadewi', 'fdcd241b81024ffc7307d10e933580ca', 'tenaga ahli', 'TALING'),
(86, 'fachri rahmadani pratama', 'fachrirahmadanipratama', 'fdcd241b81024ffc7307d10e933580ca', 'tenaga ahli', 'TALING'),
(87, 'nurul paujiah', 'nurulpaujiah', 'fdcd241b81024ffc7307d10e933580ca', 'tenaga ahli', 'TALING'),
(88, 'tri lutfi nawawi', 'trilutfinawawi', 'fdcd241b81024ffc7307d10e933580ca', 'tenaga ahli', 'TALING'),
(89, 'zaizafun zahra', 'zaizafunzahra', 'fdcd241b81024ffc7307d10e933580ca', 'tenaga ahli', 'TALING'),
(90, 'adilla', 'adilla', 'fdcd241b81024ffc7307d10e933580ca', 'tenaga ahli', 'TALING'),
(91, 'azrul azwar', 'azrulazwar', 'fdcd241b81024ffc7307d10e933580ca', 'tenaga ahli', 'TALING'),
(92, 'asliansyah', 'asliansyah', 'fdcd241b81024ffc7307d10e933580ca', 'pengadministrasi perkantoran', 'TALING'),
(93, 'm. akmal hakim', 'makmalhakim', 'fdcd241b81024ffc7307d10e933580ca', 'pengendali dampak lingkungan ahli pertama', 'TALING'),
(94, 'ahmad sairoji', 'ahmadsairoji', 'fdcd241b81024ffc7307d10e933580ca', 'penata layanan operasional', 'TALING');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
