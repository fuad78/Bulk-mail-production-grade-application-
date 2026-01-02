<x-app-layout>
    <div class="bg-gray-50 min-h-screen">
        <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        <div class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700">
                            <span class="mr-2">Administration</span>
                            <span class="mx-2">/</span>
                            <span class="text-gray-900 font-bold">Access Control</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Tabs -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex space-x-8 -mb-px">
                    <a href="{{ route('users.index') }}"
                        class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm">
                        All Users
                    </a>
                    <a href="#"
                        class="border-indigo-500 text-indigo-600 whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm">
                        Access Control
                    </a>
                    <a href="{{ route('admin.audit.index') }}"
                        class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm">
                        Audit Logs
                    </a>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Role Definitions & Capabilities
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Overview of system roles and their permissions.
                    </p>
                </div>
                <div class="p-0">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Feature</th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Admin</th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Manager</th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    User</th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Viewer</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">User
                                    Management</td>
                                <td class="px-6 py-4 text-center"><span class="text-green-600">✓</span></td>
                                <td class="px-6 py-4 text-center"><span class="text-red-400">✗</span></td>
                                <td class="px-6 py-4 text-center"><span class="text-red-400">✗</span></td>
                                <td class="px-6 py-4 text-center"><span class="text-red-400">✗</span></td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Create
                                    Campaigns</td>
                                <td class="px-6 py-4 text-center"><span class="text-green-600">✓</span></td>
                                <td class="px-6 py-4 text-center"><span class="text-green-600">✓</span></td>
                                <td class="px-6 py-4 text-center"><span class="text-green-600">✓</span></td>
                                <td class="px-6 py-4 text-center"><span class="text-red-400">✗</span></td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Approve
                                    Campaigns</td>
                                <td class="px-6 py-4 text-center"><span class="text-green-600">✓</span></td>
                                <td class="px-6 py-4 text-center"><span class="text-green-600">✓</span> (Dept)</td>
                                <td class="px-6 py-4 text-center"><span class="text-red-400">✗</span></td>
                                <td class="px-6 py-4 text-center"><span class="text-red-400">✗</span></td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">View Reports
                                </td>
                                <td class="px-6 py-4 text-center"><span class="text-green-600">✓</span></td>
                                <td class="px-6 py-4 text-center"><span class="text-green-600">✓</span></td>
                                <td class="px-6 py-4 text-center"><span class="text-green-600">✓</span></td>
                                <td class="px-6 py-4 text-center"><span class="text-green-600">✓</span></td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Address Book
                                    (View)</td>
                                <td class="px-6 py-4 text-center"><span class="text-green-600">✓</span></td>
                                <td class="px-6 py-4 text-center"><span class="text-green-600">✓</span></td>
                                <td class="px-6 py-4 text-center"><span class="text-green-600">✓</span></td>
                                <td class="px-6 py-4 text-center"><span class="text-green-600">✓</span></td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Address Book
                                    (Manage)</td>
                                <td class="px-6 py-4 text-center"><span class="text-green-600">✓</span></td>
                                <td class="px-6 py-4 text-center"><span class="text-green-600">✓</span></td>
                                <td class="px-6 py-4 text-center"><span class="text-green-600">✓</span></td>
                                <td class="px-6 py-4 text-center"><span class="text-red-400">✗</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>