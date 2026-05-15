import 'package:flutter/material.dart';

class ListViewPage extends StatelessWidget {
  const ListViewPage({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('ListView Widget'),
        backgroundColor: Theme.of(context).colorScheme.inversePrimary,
      ),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          Card(
            color: Colors.blue.shade50,
            elevation: 2,
            child: const ListTile(
              leading: CircleAvatar(
                backgroundColor: Colors.blue,
                child: Text('A', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
              ),
              title: Text('Item A', style: TextStyle(fontWeight: FontWeight.bold)),
              subtitle: Text('Ini adalah item pertama dalam ListView'),
            ),
          ),
          const SizedBox(height: 8),
          Card(
            color: Colors.green.shade50,
            elevation: 2,
            child: const ListTile(
              leading: CircleAvatar(
                backgroundColor: Colors.green,
                child: Text('B', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
              ),
              title: Text('Item B', style: TextStyle(fontWeight: FontWeight.bold)),
              subtitle: Text('Ini adalah item kedua dalam ListView'),
            ),
          ),
          const SizedBox(height: 8),
          Card(
            color: Colors.orange.shade50,
            elevation: 2,
            child: const ListTile(
              leading: CircleAvatar(
                backgroundColor: Colors.orange,
                child: Text('C', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
              ),
              title: Text('Item C', style: TextStyle(fontWeight: FontWeight.bold)),
              subtitle: Text('Ini adalah item ketiga dalam ListView'),
            ),
          ),
        ],
      ),
    );
  }
}
