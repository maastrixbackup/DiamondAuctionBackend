@extends('admin.layouts.app')
@section('title', 'Dashboard')
@section('content')
<section class="mb-8">
        <div
          class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4"
        >
          <div class="flex items-center">
            <i data-lucide="users" class="w-7 h-7 brand-gold mr-3"></i>
            <h1 class="text-2xl font-bold font-aleo text-gray-900">
              Manage Users
            </h1>
          </div>
          <button
            class="flex items-center px-5 py-2 bg-brand-gold hover:bg-yellow-600 text-white rounded font-semibold shadow transition text-sm"
          >
            <i data-lucide="user-plus" class="w-5 h-5 mr-2"></i> Add User
          </button>
        </div>

        <!-- FILTERS -->
        <form
          class="bg-white rounded-xl shadow p-4 mb-5 flex flex-col lg:flex-row gap-4 items-center"
        >
          <input
            type="text"
            placeholder="Search by name or email…"
            class="w-full lg:w-56 rounded border-gray-200 px-3 py-2 text-sm"
          />
          <select class="rounded border-gray-200 px-3 py-2 text-sm">
            <option value="">All Account Types</option>
            <option>VIP</option>
            <option>Individual</option>
            <option>Company</option>
            <option>Bidder</option>
            <option>Seller</option>
            <option>Admin</option>
          </select>
          <select class="rounded border-gray-200 px-3 py-2 text-sm">
            <option value="">All KYC Status</option>
            <option>Pending</option>
            <option>Approved</option>
            <option>Rejected</option>
          </select>
          <select class="rounded border-gray-200 px-3 py-2 text-sm">
            <option value="">All Account Status</option>
            <option>Active</option>
            <option>Suspended</option>
            <option>Disabled</option>
          </select>

          <button
            type="submit"
            class="bg-gray-200 hover:bg-brand-gold/20 text-gray-700 rounded px-3 py-2 text-sm font-semibold transition"
          >
            Filter
          </button>
        </form>

        <!-- USER TABLE -->
        <div class="overflow-x-auto bg-white rounded-xl shadow">
          <form>
            <!-- Bulk Actions -->
            <div
              class="flex items-center p-3 border-b border-gray-100 bg-gray-50"
            >
              <input type="checkbox" class="mr-2" id="bulk-select" />
              <label for="bulk-select" class="mr-4 text-sm text-gray-700"
                >Select all</label
              >
              <button
                type="button"
                class="ml-2 px-3 py-1 bg-green-600 text-white rounded text-xs font-semibold hover:bg-green-700 transition"
              >
                Approve
              </button>
              <button
                type="button"
                class="ml-2 px-3 py-1 bg-red-600 text-white rounded text-xs font-semibold hover:bg-red-700 transition"
              >
                Reject
              </button>
              <button
                type="button"
                class="ml-2 px-3 py-1 bg-gray-600 text-white rounded text-xs font-semibold hover:bg-gray-700 transition"
              >
                Disable
              </button>
            </div>
            <table class=" table table-responsive min-w-full text-sm text-left">
              <thead>
                <tr class="bg-gray-50 border-b text-xs text-gray-700">
                  <th class="px-3 py-2"><input type="checkbox" /></th>
                  <th class="px-3 py-2">User ID / Name</th>
                  <th class="px-3 py-2">Email</th>
                  <th class="px-3 py-2">Type</th>
                  <th class="px-3 py-2">Registration</th>
                  <th class="px-3 py-2">KYC Status</th>
                  <th class="px-3 py-2">Account Status</th>
                  <th class="px-3 py-2">Last Login</th>
                  <th class="px-3 py-2">Actions</th>
                </tr>
              </thead>
              <tbody>
                <!-- Sample: VIP Buyer -->
                <tr class="border-b hover:bg-gray-50">
                  <td class="px-3 py-2"><input type="checkbox" /></td>
                  <td class="px-3 py-2 flex items-center space-x-2">
                    <span
                      class="rounded px-2 py-0.5 bg-yellow-100 text-yellow-800 text-xs font-semibold"
                      >VIP</span
                    >
                    <a
                      href="user-profile.html"
                      class="font-semibold text-blue-700 hover:underline"
                      >#001 Jay Carter</a
                    >
                  </td>
                  <td class="px-3 py-2">jay.carter@carterluxury.com</td>
                  <td class="px-3 py-2">Buyer (Individual)</td>
                  <td class="px-3 py-2">24 May 2024</td>
                  <td class="px-3 py-2">
                    <span
                      class="bg-green-100 text-green-800 px-2 py-0.5 rounded text-xs font-semibold"
                      >Approved</span
                    >
                  </td>
                  <td class="px-3 py-2">
                    <span
                      class="bg-green-100 text-green-800 px-2 py-0.5 rounded text-xs font-semibold"
                      >Active</span
                    >
                  </td>
                  <td class="px-3 py-2">22 May 2025</td>
                  <td class="px-3 py-2 flex flex-wrap gap-1">
                    <a
                      href="user-profile.html"
                      class="text-blue-700 hover:underline flex items-center"
                      ><i data-lucide="user" class="w-4 h-4 mr-1"></i>View</a
                    >
                    <button
                      type="button"
                      class="text-green-700 flex items-center px-2 py-1 hover:bg-green-100 rounded"
                    >
                      <i data-lucide="check" class="w-4 h-4 mr-1"></i>Approve
                    </button>
                    <button
                      type="button"
                      class="text-yellow-700 flex items-center px-2 py-1 hover:bg-yellow-100 rounded"
                    >
                      <i data-lucide="slash" class="w-4 h-4 mr-1"></i>Suspend
                    </button>
                    <button
                      type="button"
                      class="text-gray-600 flex items-center px-2 py-1 hover:bg-gray-100 rounded"
                    >
                      <i data-lucide="edit" class="w-4 h-4 mr-1"></i>Edit
                    </button>
                    <button
                      type="button"
                      class="text-red-700 flex items-center px-2 py-1 hover:bg-red-100 rounded"
                    >
                      <i data-lucide="trash" class="w-4 h-4 mr-1"></i>Delete
                    </button>
                    <button
                      type="button"
                      class="text-gray-500 flex items-center px-2 py-1 hover:bg-gray-100 rounded"
                    >
                      <i data-lucide="message-square" class="w-4 h-4 mr-1"></i
                      >Notes
                    </button>
                  </td>
                </tr>
                <!-- Sample: Company Seller -->
                <tr class="border-b hover:bg-gray-50">
                  <td class="px-3 py-2"><input type="checkbox" /></td>
                  <td class="px-3 py-2 flex items-center space-x-2">
                    <span
                      class="rounded px-2 py-0.5 bg-blue-100 text-blue-800 text-xs font-semibold"
                      >Company</span
                    >
                    <a
                      href="user-profile.html"
                      class="font-semibold text-blue-700 hover:underline"
                      >#002 London Gem Traders Ltd</a
                    >
                  </td>
                  <td class="px-3 py-2">contact@lgtltd.co.uk</td>
                  <td class="px-3 py-2">Seller (Company)</td>
                  <td class="px-3 py-2">13 Jan 2024</td>
                  <td class="px-3 py-2">
                    <span
                      class="bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded text-xs font-semibold"
                      >Pending</span
                    >
                  </td>
                  <td class="px-3 py-2">
                    <span
                      class="bg-green-100 text-green-800 px-2 py-0.5 rounded text-xs font-semibold"
                      >Active</span
                    >
                  </td>
                  <td class="px-3 py-2">20 May 2025</td>
                  <td class="px-3 py-2 flex flex-wrap gap-1">
                    <a
                      href="user-profile.html"
                      class="text-blue-700 hover:underline flex items-center"
                      ><i data-lucide="user" class="w-4 h-4 mr-1"></i>View</a
                    >
                    <button
                      type="button"
                      class="text-green-700 flex items-center px-2 py-1 hover:bg-green-100 rounded"
                    >
                      <i data-lucide="check" class="w-4 h-4 mr-1"></i>Approve
                    </button>
                    <button
                      type="button"
                      class="text-yellow-700 flex items-center px-2 py-1 hover:bg-yellow-100 rounded"
                    >
                      <i data-lucide="slash" class="w-4 h-4 mr-1"></i>Suspend
                    </button>
                    <button
                      type="button"
                      class="text-gray-600 flex items-center px-2 py-1 hover:bg-gray-100 rounded"
                    >
                      <i data-lucide="edit" class="w-4 h-4 mr-1"></i>Edit
                    </button>
                    <button
                      type="button"
                      class="text-red-700 flex items-center px-2 py-1 hover:bg-red-100 rounded"
                    >
                      <i data-lucide="trash" class="w-4 h-4 mr-1"></i>Delete
                    </button>
                    <button
                      type="button"
                      class="text-gray-500 flex items-center px-2 py-1 hover:bg-gray-100 rounded"
                    >
                      <i data-lucide="message-square" class="w-4 h-4 mr-1"></i
                      >Notes
                    </button>
                  </td>
                </tr>
                <!-- Sample: Individual Seller -->
                <tr class="border-b hover:bg-gray-50">
                  <td class="px-3 py-2"><input type="checkbox" /></td>
                  <td class="px-3 py-2 flex items-center space-x-2">
                    <span
                      class="rounded px-2 py-0.5 bg-green-100 text-green-800 text-xs font-semibold"
                      >Individual</span
                    >
                    <a
                      href="user-profile.html"
                      class="font-semibold text-blue-700 hover:underline"
                      >#003 Priya Desai</a
                    >
                  </td>
                  <td class="px-3 py-2">priya.desai@email.com</td>
                  <td class="px-3 py-2">Seller (Individual)</td>
                  <td class="px-3 py-2">28 Feb 2024</td>
                  <td class="px-3 py-2">
                    <span
                      class="bg-green-100 text-green-800 px-2 py-0.5 rounded text-xs font-semibold"
                      >Approved</span
                    >
                  </td>
                  <td class="px-3 py-2">
                    <span
                      class="bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded text-xs font-semibold"
                      >Suspended</span
                    >
                  </td>
                  <td class="px-3 py-2">14 May 2025</td>
                  <td class="px-3 py-2 flex flex-wrap gap-1">
                    <a
                      href="user-profile.html"
                      class="text-blue-700 hover:underline flex items-center"
                      ><i data-lucide="user" class="w-4 h-4 mr-1"></i>View</a
                    >
                    <button
                      type="button"
                      class="text-green-700 flex items-center px-2 py-1 hover:bg-green-100 rounded"
                    >
                      <i data-lucide="check" class="w-4 h-4 mr-1"></i>Approve
                    </button>
                    <button
                      type="button"
                      class="text-yellow-700 flex items-center px-2 py-1 hover:bg-yellow-100 rounded"
                    >
                      <i data-lucide="slash" class="w-4 h-4 mr-1"></i>Reactivate
                    </button>
                    <button
                      type="button"
                      class="text-gray-600 flex items-center px-2 py-1 hover:bg-gray-100 rounded"
                    >
                      <i data-lucide="edit" class="w-4 h-4 mr-1"></i>Edit
                    </button>
                    <button
                      type="button"
                      class="text-red-700 flex items-center px-2 py-1 hover:bg-red-100 rounded"
                    >
                      <i data-lucide="trash" class="w-4 h-4 mr-1"></i>Delete
                    </button>
                    <button
                      type="button"
                      class="text-gray-500 flex items-center px-2 py-1 hover:bg-gray-100 rounded"
                    >
                      <i data-lucide="message-square" class="w-4 h-4 mr-1"></i
                      >Notes
                    </button>
                  </td>
                </tr>
                <!-- Sample: Admin -->
                <tr class="border-b hover:bg-gray-50">
                  <td class="px-3 py-2"><input type="checkbox" /></td>
                  <td class="px-3 py-2 flex items-center space-x-2">
                    <span
                      class="rounded px-2 py-0.5 bg-gray-300 text-gray-700 text-xs font-semibold"
                      >Admin</span
                    >
                    <a
                      href="user-profile.html"
                      class="font-semibold text-blue-700 hover:underline"
                      >#004 Admin Account</a
                    >
                  </td>
                  <td class="px-3 py-2">admin@dexgems.com</td>
                  <td class="px-3 py-2">Admin</td>
                  <td class="px-3 py-2">10 Jan 2023</td>
                  <td class="px-3 py-2">
                    <span
                      class="bg-green-100 text-green-800 px-2 py-0.5 rounded text-xs font-semibold"
                      >Approved</span
                    >
                  </td>
                  <td class="px-3 py-2">
                    <span
                      class="bg-green-100 text-green-800 px-2 py-0.5 rounded text-xs font-semibold"
                      >Active</span
                    >
                  </td>
                  <td class="px-3 py-2">Today</td>
                  <td class="px-3 py-2 flex flex-wrap gap-1">
                    <a
                      href="user-profile.html"
                      class="text-blue-700 hover:underline flex items-center"
                      ><i data-lucide="user" class="w-4 h-4 mr-1"></i>View</a
                    >
                    <button
                      type="button"
                      class="text-gray-600 flex items-center px-2 py-1 hover:bg-gray-100 rounded"
                    >
                      <i data-lucide="edit" class="w-4 h-4 mr-1"></i>Edit
                    </button>
                    <button
                      type="button"
                      class="text-red-700 flex items-center px-2 py-1 hover:bg-red-100 rounded"
                    >
                      <i data-lucide="trash" class="w-4 h-4 mr-1"></i>Delete
                    </button>
                    <button
                      type="button"
                      class="text-gray-500 flex items-center px-2 py-1 hover:bg-gray-100 rounded"
                    >
                      <i data-lucide="message-square" class="w-4 h-4 mr-1"></i
                      >Notes
                    </button>
                  </td>
                </tr>
                <!-- Add more as needed -->
              </tbody>
            </table>
          </form>
        </div>
      </section>
@endsection
