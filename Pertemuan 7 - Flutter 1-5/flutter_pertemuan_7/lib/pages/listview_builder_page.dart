import 'package:flutter/material.dart';

class ListViewBuilderPage extends StatelessWidget {
  const ListViewBuilderPage({super.key});

  @override
  Widget build(BuildContext context) {
    // Data array
    final List<Map<String, dynamic>> mahasiswa = [
      {'nama': 'Daffa Hakim', 'nim': '2311102055', 'jurusan': 'Informatika'},
      {'nama': 'Ahmad Rizki', 'nim': '2311102001', 'jurusan': 'Informatika'},
      {'nama': 'Budi Santoso', 'nim': '2311102010', 'jurusan': 'Informatika'},
      {'nama': 'Citra Dewi', 'nim': '2311102020', 'jurusan': 'Informatika'},
      {'nama': 'Diana Putri', 'nim': '2311102030', 'jurusan': 'Informatika'},
      {'nama': 'Eko Prasetyo', 'nim': '2311102040', 'jurusan': 'Informatika'},
      {'nama': 'Fani Lestari', 'nim': '2311102050', 'jurusan': 'Informatika'},
    ];

    return Scaffold(
      appBar: AppBar(
        title: const Text('ListView.builder Widget'),
        backgroundColor: Theme.of(context).colorScheme.inversePrimary,
      ),
      body: ListView.builder(
        padding: const EdgeInsets.all(16),
        itemCount: mahasiswa.length,
        itemBuilder: (context, index) {
          final mhs = mahasiswa[index];
          return Card(
            margin: const EdgeInsets.only(bottom: 10),
            elevation: 2,
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(12),
            ),
            child: ListTile(
              leading: CircleAvatar(
                backgroundColor: Colors.indigo,
                child: Text(
                  '${index + 1}',
                  style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold),
                ),
              ),
              title: Text(
                mhs['nama'] as String,
                style: const TextStyle(fontWeight: FontWeight.bold),
              ),
              subtitle: Text('NIM: ${mhs['nim']} | ${mhs['jurusan']}'),
              trailing: const Icon(Icons.school, color: Colors.indigo),
            ),
          );
        },
      ),
    );
  }
}
