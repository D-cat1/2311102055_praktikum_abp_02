import 'package:flutter/material.dart';

class ListViewSeparatedPage extends StatelessWidget {
  const ListViewSeparatedPage({super.key});

  @override
  Widget build(BuildContext context) {
    final List<Map<String, dynamic>> menu = [
      {'nama': 'Nasi Goeng', 'harga': 'Rp 15.000', 'icon': Icons.restaurant},
      {'nama': 'Mie Ayam', 'harga': 'Rp 12.000', 'icon': Icons.ramen_dining},
      {'nama': 'Soto Ayam', 'harga': 'Rp 13.000', 'icon': Icons.soup_kitchen},
      {'nama': 'Es Teh', 'harga': 'Rp 5.000', 'icon': Icons.local_cafe},
      {'nama': 'Jus Jeruk', 'harga': 'Rp 8.000', 'icon': Icons.local_drink},
      {'nama': 'Kopi Susu', 'harga': 'Rp 10.000', 'icon': Icons.coffee},
    ];

    return Scaffold(
      appBar: AppBar(
        title: const Text('ListView.separated Widget'),
        backgroundColor: Theme.of(context).colorScheme.inversePrimary,
      ),
      body: ListView.separated(
        padding: const EdgeInsets.all(16),
        itemCount: menu.length,
        separatorBuilder: (context, index) =>
            const Divider(thickness: 1, height: 1, color: Colors.grey),
        itemBuilder: (context, index) {
          final item = menu[index];
          return ListTile(
            leading: Icon(
              item['icon'] as IconData,
              size: 32,
              color: Colors.teal,
            ),
            title: Text(
              item['nama'] as String,
              style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
            ),
            subtitle: Text(
              item['harga'] as String,
              style: const TextStyle(
                color: Colors.green,
                fontWeight: FontWeight.w600,
              ),
            ),
            trailing: const Icon(Icons.add_shopping_cart, color: Colors.teal),
          );
        },
      ),
    );
  }
}
