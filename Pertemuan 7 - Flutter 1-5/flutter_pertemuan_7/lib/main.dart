import 'package:flutter/material.dart';

import 'pages/container_page.dart';
import 'pages/gridview_page.dart';
import 'pages/listview_page.dart';
import 'pages/listview_builder_page.dart';
import 'pages/listview_separated_page.dart';
import 'pages/stack_page.dart';

void main() {
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Flutter Widget - P7',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(seedColor: Colors.indigo),
        useMaterial3: true,
      ),
      home: const HomePage(),
    );
  }
}

class HomePage extends StatelessWidget {
  const HomePage({super.key});

  @override
  Widget build(BuildContext context) {
    final menuItems = [
      _MenuItem(
        'Container',
        Icons.square_rounded,
        Colors.blue,
        const ContainerPage(),
      ),
      _MenuItem(
        'GridView',
        Icons.grid_view,
        const Color.fromARGB(255, 175, 76, 79),
        const GridViewPage(),
      ),
      _MenuItem('ListView', Icons.list, Colors.orange, const ListViewPage()),
      _MenuItem(
        'ListView.builder',
        Icons.build,
        Colors.purple,
        const ListViewBuilderPage(),
      ),
      _MenuItem(
        'ListView.separated',
        Icons.view_list,
        const Color.fromARGB(255, 0, 150, 77),
        const ListViewSeparatedPage(),
      ),
      _MenuItem('Stack', Icons.layers, Colors.red, const StackPage()),
    ];

    return Scaffold(
      appBar: AppBar(
        title: const Text('Flutter Widget - P7'),
        backgroundColor: Theme.of(context).colorScheme.inversePrimary,
      ),
      body: ListView.builder(
        padding: const EdgeInsets.all(16),
        itemCount: menuItems.length,
        itemBuilder: (context, index) {
          final item = menuItems[index];
          return Card(
            margin: const EdgeInsets.only(bottom: 12),
            elevation: 2,
            child: ListTile(
              leading: CircleAvatar(
                backgroundColor: item.color,
                child: Icon(item.icon, color: Colors.white),
              ),
              title: Text(
                item.title,
                style: const TextStyle(
                  fontWeight: FontWeight.bold,
                  fontSize: 16,
                ),
              ),
              trailing: const Icon(Icons.arrow_forward_ios),
              onTap: () {
                Navigator.push(
                  context,
                  MaterialPageRoute(builder: (context) => item.page),
                );
              },
            ),
          );
        },
      ),
    );
  }
}

class _MenuItem {
  final String title;
  final IconData icon;
  final Color color;
  final Widget page;

  _MenuItem(this.title, this.icon, this.color, this.page);
}
