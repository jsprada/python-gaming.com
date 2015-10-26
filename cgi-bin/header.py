#!/usr/bin/python3
class Menu:
    f = open('../menu/menu.shtml')
    content = f.read()
    @staticmethod
    def get_content():
        return Menu.content

