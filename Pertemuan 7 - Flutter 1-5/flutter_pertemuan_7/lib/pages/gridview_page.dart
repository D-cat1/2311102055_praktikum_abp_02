import 'package:flutter/material.dart';

class GridViewPage extends StatelessWidget {
  const GridViewPage({super.key});

  @override
  Widget build(BuildContext context) {
    final gridItems = [
      {'icon': Icons.home, 'label': 'Home', 'color': Colors.blue},
      {'icon': Icons.star, 'label': 'Favorit', 'color': Colors.amber},
      {'icon': Icons.person, 'label': 'Profil', 'color': Colors.green},
      {'icon': Icons.settings, 'label': 'Setting', 'color': Colors.grey},
      {'icon': Icons.camera, 'label': 'Kamera', 'color': Colors.purple},
      {'icon': Icons.music_note, 'label': 'Musik', 'color': Colors.red},
      {'icon': Icons.email, 'label': 'Email', 'color': Colors.teal},
      {'icon': Icons.phone, 'label': 'Telepon', 'color': Colors.orange},
    ];

    return Scaffold(
      appBar: AppBar(
        title: const Text('GridView Widget'),
        backgroundColor: Theme.of(context).colorScheme.inversePrimary,
      ),
      body: Padding(
        padding: const EdgeInsets.all(16),
        child: GridView.builder(
          gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
            crossAxisCount: 2,
            crossAxisSpacing: 16,
            mainAxisSpacing: 16,
          ),
          itemCount: gridItems.length,
          itemBuilder: (context, index) {
            final item = gridItems[index];
            return Card(
              elevation: 4,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(16),
              ),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(
                    item['icon'] as IconData,
                    size: 48,
                    color: item['color'] as Color,
                  ),
                  const SizedBox(height: 8),
                  Text(
                    item['label'] as String,
                    style: const TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ],
              ),
            );
          },
        ),
      ),
    );
  }
}
