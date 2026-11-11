  <?php require_once "includes/db_connection.php"; ?>
  <?php require_once "includes/functions.php"; ?>
  <?php require_once "includes/header.php"; ?>

  <!-- ================= HERO ================= -->
  <section class="bg-black text-white py-16 sm:py-24">
      <div
          class="max-w-7xl mx-auto px-4 sm:px-6 grid lg:grid-cols-2 gap-12 items-center">
          <!-- Left -->
          <div>
              <p
                  class="uppercase tracking-[3px] text-xs sm:text-sm text-gray-400 mb-4">
                  Smart Lost & Found Platform
              </p>

              <h2
                  class="text-4xl sm:text-5xl lg:text-6xl font-bold leading-tight mb-6">
                  Recover Lost Items Faster
              </h2>

              <p class="text-base sm:text-lg text-gray-300 mb-8 leading-relaxed">
                  Report lost items and help others recover their belongings.
              </p>

              <div class="flex flex-col sm:flex-row gap-4">
                  <a href="report.php"
                      class="px-6 py-4 bg-white text-black rounded-2xl font-semibold hover:scale-105 transition">
                      Report Lost Item
</a>

                  <a href="items.php"
                      class="px-6 py-4 border border-white rounded-2xl text-center hover:bg-white hover:text-black transition">
                      Browse Items
                  </a>
              </div>
          </div>

          <!-- Search Box -->
          <div
              class="bg-white/10 border border-white/20 backdrop-blur-xl rounded-3xl p-6 sm:p-8">
              <h3 class="text-2xl font-semibold mb-6">Quick Search</h3>

              <form
                  method="GET"
                  action="items.php"
                  class="space-y-4">

                  <input
                      type="text"
                      name="search"
                      placeholder="Search item"
                      value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                      class="w-full p-4 rounded-xl bg-white/10 border border-white/20 outline-none text-white placeholder:text-gray-300" />

                  <input
                      type="text"
                      name="location"
                      placeholder="Location"
                      value="<?= htmlspecialchars($_GET['location'] ?? '') ?>"
                      class="w-full p-4 rounded-xl bg-white/10 border border-white/20 outline-none text-white placeholder:text-gray-300" />

                  <button
                      type="submit"
                      class="w-full py-4 bg-white text-black rounded-xl font-semibold hover:bg-gray-200 transition">
                      Search
                  </button>

              </form>
          </div>
      </div>
  </section>

  <!-- ================= FEATURES ================= -->
  <section class="py-16 sm:py-20 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6">
          <div class="text-center mb-14">
              <p class="uppercase tracking-[3px] text-sm text-gray-500 mb-4">
                  Why Choose Us
              </p>

              <h2 class="text-3xl sm:text-5xl font-bold mb-6">
                  Modern Lost & Found Experience
              </h2>

              <p class="text-gray-600 max-w-2xl mx-auto text-base sm:text-lg">
                  Helping people reconnect with their belongings through a secure and
                  fast platform.
              </p>
          </div>

          <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
              <!-- Card -->
              <div class="bg-gray-100 rounded-3xl p-8 hover:shadow-2xl transition">
                  <div
                      class="w-16 h-16 rounded-2xl bg-black text-white flex items-center justify-center text-2xl mb-6">
                      🔍
                  </div>

                  <h3 class="text-2xl font-semibold mb-4">Smart Search</h3>

                  <p class="text-gray-600 leading-relaxed">
                      Quickly search lost and found items using category and location.
                  </p>
              </div>

              <!-- Card -->
              <div class="bg-gray-100 rounded-3xl p-8 hover:shadow-2xl transition">
                  <div
                      class="w-16 h-16 rounded-2xl bg-black text-white flex items-center justify-center text-2xl mb-6">
                      📍
                  </div>

                  <h3 class="text-2xl font-semibold mb-4">Location Tracking</h3>

                  <p class="text-gray-600 leading-relaxed">
                      Find where items were lost or found with accurate location
                      information.
                  </p>
              </div>

              <!-- Card -->
              <div class="bg-gray-100 rounded-3xl p-8 hover:shadow-2xl transition">
                  <div
                      class="w-16 h-16 rounded-2xl bg-black text-white flex items-center justify-center text-2xl mb-6">
                      🔒
                  </div>

                  <h3 class="text-2xl font-semibold mb-4">Secure Platform</h3>

                  <p class="text-gray-600 leading-relaxed">
                      Safe communication and verified reports.
                  </p>
              </div>
          </div>
      </div>
  </section>
  <section id="lost-items" class="py-20">
      <div class="max-w-7xl mx-auto px-6">

          <div class="flex items-center justify-between mb-10">
              <h2 class="text-4xl font-bold text-black">Items</h2>
          </div>

          <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">

              <?php

                $query = "SELECT 
                        id,
                        title,
                        location,
                        report_type,
                        image,
                        created_at
                      FROM reports
                      WHERE status='approved'
                      ORDER BY created_at DESC
                      LIMIT 6";
                $stmt = $pdo->prepare($query);
                $stmt->execute();

                while ($item = $stmt->fetch()):

                ?>

                  <div class="bg-white rounded-3xl overflow-hidden shadow-md hover:shadow-2xl transition">

                      <!-- Image -->
                      <div class="h-48 overflow-hidden">

                          <img
                              src="<?= htmlspecialchars(reportImageSrc($item['image'])) ?>"
                              alt="<?= htmlspecialchars($item['title']) ?>"
                              class="w-full h-full object-cover hover:scale-110 transition duration-500" />

                      </div>

                      <!-- Content -->
                      <div class="p-6">

                          <!-- Status -->
                          <span class="<?= $item['report_type'] == 'lost'
                                            ? 'bg-red-100 text-red-600'
                                            : 'bg-green-100 text-green-600' ?>
                            px-3 py-1 rounded-full text-sm">

                              <?= ucfirst($item['report_type']) ?>

                          </span>

                          <!-- Title -->
                          <h3 class="text-xl font-semibold mt-4 mb-2">
                              <?= htmlspecialchars($item['title']) ?>
                          </h3>

                          <!-- Location -->
                          <p class="text-gray-600 mb-4">
                              <?= htmlspecialchars($item['location']) ?>
                          </p>

                          <!-- Button -->
                          <a
                              href="single.php?id=<?= $item['id'] ?>"
                              class="block text-center w-full py-3 bg-black text-white rounded-2xl hover:opacity-90 transition">

                              View Details

                          </a>

                      </div>

                  </div>

              <?php endwhile; ?>

          </div>

      </div>
  </section>

  <!-- ================= CTA ================= -->
  <section class="py-16 sm:py-24 bg-white">
      <div class="max-w-5xl mx-auto px-4 sm:px-6">
          <div
              class="bg-black text-white rounded-[30px] sm:rounded-[40px] py-14 sm:py-16 px-6 sm:px-10 text-center">
              <h2 class="text-3xl sm:text-5xl font-extrabold mb-5 leading-tight">
                  Lost Something Recently?
              </h2>

              <p class="text-gray-300 text-base sm:text-lg mb-8 sm:mb-10">
                  Report it now and increase your chances of recovery.
              </p>

              <a href="report.php"
                  class="bg-white text-black px-8 sm:px-10 py-4 rounded-2xl font-semibold hover:bg-gray-200 transition">
                  Create Report
                </a>
          </div>
      </div>
  </section>
  <?php include "includes/footer.php"; ?>
