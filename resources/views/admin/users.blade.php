@extends('layouts.app')

@section('content')
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-100 px-8 py-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Manajemen Pengguna</h1>
                <p class="text-gray-600 mt-2">Kelola pengguna sistem BMKG STAMAR</p>
            </div>

            <div class="flex items-center space-x-4">
                <select id="userRoleFilter" class="p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Role</option>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>
                <button onclick="showCreateUserModal()"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-3 rounded-xl font-medium transition-colors">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                        </path>
                    </svg>
                    Tambah Pengguna
                </button>
                <button onclick="loadUsers()"
                    class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-3 rounded-xl font-medium transition-colors">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                        </path>
                    </svg>
                    Refresh
                </button>
            </div>
        </div>
    </header>

    <!-- Content -->
    <div class="p-8">
        <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
            <div class="overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Nama</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Email</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Role</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Terdaftar</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody" class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    <div class="flex items-center justify-center">
                                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-400"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        Loading...
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Create/Edit User Modal -->
    <div id="userModal"
        class="modal-overlay hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 w-full max-w-md mx-4">
            <div class="flex justify-between items-center mb-6">
                <h3 id="userModalTitle" class="text-2xl font-bold text-gray-900">Tambah Pengguna</h3>
                <button onclick="hideModal('userModal')" class="text-gray-500 hover:text-gray-700 p-2">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <form id="userForm" class="space-y-6">
                @csrf
                <input type="hidden" id="userId" name="user_id">

                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-3">Nama Lengkap</label>
                    <input type="text" id="name" name="name" required
                        class="w-full p-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500"
                        placeholder="Masukkan nama lengkap...">
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-3">Email</label>
                    <input type="email" id="email" name="email" required
                        class="w-full p-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500"
                        placeholder="Masukkan email...">
                </div>

                <div id="passwordField">
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-3">Password</label>
                    <input type="password" id="password" name="password"
                        class="w-full p-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500"
                        placeholder="Masukkan password...">
                </div>

                <div>
                    <label for="phone" class="block text-sm font-semibold text-gray-700 mb-3">No. Telepon</label>
                    <input type="text" id="phone" name="phone"
                        class="w-full p-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500"
                        placeholder="Masukkan no. telepon...">
                </div>

                <div>
                    <label for="role" class="block text-sm font-semibold text-gray-700 mb-3">Role</label>
                    <select id="role" name="role" required
                        class="w-full p-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
                        <option value="">Pilih role</option>
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                    </select>
                </div>

                <div class="pt-6 border-t border-gray-200">
                    <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 px-6 rounded-xl transition-all duration-200">
                        Simpan Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- User Detail Modal -->
    <div id="userDetailModal"
        class="modal-overlay hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-900">Detail Pengguna</h3>
                <button onclick="hideModal('userDetailModal')" class="text-gray-500 hover:text-gray-700 p-2">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <div id="userDetailContent">
                <!-- Content will be populated by JavaScript -->
            </div>
        </div>
    </div>

    <script src="{{ asset('js/admin/users.js') }}"></script>
@endsection
