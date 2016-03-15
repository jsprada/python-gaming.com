import pygame as pg
import os

pg.init()

os.environ['SDL_VIDEO_WINDOW_POS'] = '{},{}'.format(100,200)
screen = pg.display.set_mode((800,600), pg.RESIZABLE)
done = False
while not done:
    for event in pg.event.get():
        if event.type == pg.QUIT:
            done = True

    pg.display.update()
