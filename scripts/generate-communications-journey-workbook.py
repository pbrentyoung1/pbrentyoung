#!/usr/bin/env python3
"""Generate the printable Whole Journey Communications Audit workbook."""

import math
import os
import shutil

from reportlab.lib.colors import Color, HexColor, white
from reportlab.lib.pagesizes import letter
from reportlab.pdfbase import pdfmetrics
from reportlab.pdfbase.ttfonts import TTFont
from reportlab.pdfgen import canvas


ROOT = os.path.abspath(os.path.join(os.path.dirname(__file__), ".."))
OUTPUT = os.path.join(ROOT, "output", "pdf", "whole-journey-communications-audit.pdf")
SITE_COPY = os.path.join(ROOT, "assets", "pdf", "whole-journey-communications-audit.pdf")

PAGE_W, PAGE_H = letter
MARGIN = 38

INK = HexColor("#2b2a27")
INK_SOFT = HexColor("#4a4842")
MUTE = HexColor("#6b6860")
HAIRLINE = HexColor("#c9c2b4")
PALE = HexColor("#f4f1ea")
PALE_BLUE = HexColor("#dcebf0")
BLUE = HexColor("#1f83a3")
RUST = HexColor("#c1271d")
WHITE = white

FONT_DIR = "/System/Library/Fonts/Supplemental"
FONTS = {
    "Georgia": "Georgia.ttf",
    "Georgia-Bold": "Georgia Bold.ttf",
    "Georgia-Italic": "Georgia Italic.ttf",
    "Arial": "Arial.ttf",
    "Arial-Bold": "Arial Bold.ttf",
    "Courier": "Courier New.ttf",
    "Courier-Bold": "Courier New Bold.ttf",
}

STAGES = [
    ("AWARENESS", "Who are these people?"),
    ("VISIT", "Is this worth exploring?"),
    ("ATTEND", "Do I want to come back?"),
    ("MEMBER", "Is this my church?"),
    ("MINISTER", "How can I prepare a place for someone else?"),
]


def register_fonts():
    for name, filename in FONTS.items():
        pdfmetrics.registerFont(TTFont(name, os.path.join(FONT_DIR, filename)))


def text_width(text, font, size):
    return pdfmetrics.stringWidth(text, font, size)


def wrap(text, font, size, width):
    words = text.split()
    lines = []
    current = ""
    for word in words:
        candidate = word if not current else current + " " + word
        if text_width(candidate, font, size) <= width:
            current = candidate
        else:
            if current:
                lines.append(current)
            current = word
    if current:
        lines.append(current)
    return lines


def draw_wrapped(c, text, x, y, width, font="Georgia", size=10, leading=None, color=INK_SOFT):
    leading = leading or size * 1.35
    c.setFillColor(color)
    c.setFont(font, size)
    for line in wrap(text, font, size, width):
        c.drawString(x, y, line)
        y -= leading
    return y


def draw_kicker(c, text, x, y):
    c.saveState()
    c.setFillColor(BLUE)
    c.setFont("Courier-Bold", 7.5)
    kicker = c.beginText(x, y)
    kicker.setFont("Courier-Bold", 7.5)
    kicker.setCharSpace(1.25)
    kicker.textLine(text.upper())
    c.drawText(kicker)
    c.restoreState()


def draw_rule(c, y):
    c.setStrokeColor(INK)
    c.setLineWidth(3.5)
    c.line(MARGIN, y, PAGE_W - MARGIN, y)
    c.setLineWidth(0.45)
    c.line(MARGIN, y - 4, PAGE_W - MARGIN, y - 4)


def draw_footer(c, page_number, label="WHOLE JOURNEY COMMUNICATIONS AUDIT"):
    c.setStrokeColor(HAIRLINE)
    c.setLineWidth(0.45)
    c.line(MARGIN, 31, PAGE_W - MARGIN, 31)
    c.setFillColor(MUTE)
    c.setFont("Courier", 6.5)
    c.drawString(MARGIN, 19, label)
    c.drawRightString(PAGE_W - MARGIN, 19, "BRENT YOUNG  /  %02d" % page_number)


def draw_page_title(c, kicker, title, deck, page_number):
    draw_kicker(c, kicker, MARGIN, PAGE_H - 48)
    c.setFillColor(INK)
    title_size = 25
    available = PAGE_W - MARGIN * 2
    while title_size > 18 and text_width(title, "Georgia-Bold", title_size) > available:
        title_size -= 0.5
    c.setFont("Georgia-Bold", title_size)
    c.drawString(MARGIN, PAGE_H - 79, title)
    draw_wrapped(c, deck, MARGIN, PAGE_H - 101, available - 16, "Georgia", 9.2, 12.5, INK_SOFT)
    draw_rule(c, PAGE_H - 133)
    draw_footer(c, page_number)


def polygon_points(cx, cy, radius, count=5, start_degrees=90):
    points = []
    for i in range(count):
        angle = math.radians(start_degrees - i * (360 / count))
        points.append((cx + radius * math.cos(angle), cy + radius * math.sin(angle)))
    return points


def draw_polygon(c, points, stroke=HAIRLINE, width=0.5, fill=None):
    path = c.beginPath()
    path.moveTo(points[0][0], points[0][1])
    for x, y in points[1:]:
        path.lineTo(x, y)
    path.close()
    c.setStrokeColor(stroke)
    c.setLineWidth(width)
    if fill is not None:
        c.setFillColor(fill)
        c.drawPath(path, stroke=1, fill=1)
    else:
        c.drawPath(path, stroke=1, fill=0)


def draw_star(c, points):
    order = [0, 2, 4, 1, 3, 0]
    c.setStrokeColor(Color(BLUE.red, BLUE.green, BLUE.blue, alpha=0.18))
    c.setLineWidth(0.8)
    path = c.beginPath()
    path.moveTo(points[order[0]][0], points[order[0]][1])
    for idx in order[1:]:
        path.lineTo(points[idx][0], points[idx][1])
    c.drawPath(path, stroke=1, fill=0)


def draw_stage_label(c, stage, question, x, y, align="center", width=110, question_size=6.7):
    c.setFillColor(INK)
    c.setFont("Arial-Bold", 8.5)
    if align == "left":
        draw_x = x
        c.drawString(draw_x, y, stage)
    elif align == "right":
        c.drawRightString(x, y, stage)
        draw_x = x - width
    else:
        c.drawCentredString(x, y, stage)
        draw_x = x - width / 2

    c.setFillColor(MUTE)
    c.setFont("Georgia", question_size)
    lines = wrap(question, "Georgia", question_size, width)
    qy = y - 10
    for line in lines[:3]:
        if align == "left":
            c.drawString(draw_x, qy, line)
        elif align == "right":
            c.drawRightString(x, qy, line)
        else:
            c.drawCentredString(x, qy, line)
        qy -= 8.5


def draw_journey_map(c, cx, cy, radius, show_questions=True, writing_lines=False):
    outer = polygon_points(cx, cy, radius)

    for ratio in [0.2, 0.4, 0.6, 0.8, 1.0]:
        pts = polygon_points(cx, cy, radius * ratio)
        draw_polygon(c, pts, BLUE if ratio == 1.0 else HAIRLINE, 0.9 if ratio == 1.0 else 0.35)

    draw_star(c, outer)

    c.setStrokeColor(HAIRLINE)
    c.setLineWidth(0.45)
    for x, y in outer:
        c.line(cx, cy, x, y)

    c.setFillColor(PALE)
    c.circle(cx, cy, 27, stroke=0, fill=1)
    c.setFillColor(MUTE)
    c.setFont("Courier-Bold", 5.8)
    c.drawCentredString(cx, cy + 4, "NICHE")
    c.drawCentredString(cx, cy - 5, "INSIDER")

    c.setFillColor(BLUE)
    c.setFont("Courier-Bold", 6.2)
    c.drawCentredString(cx, cy + radius - 14, "BROAD / PUBLIC")

    label_specs = [
        (0, outer[0][0], outer[0][1] + 27, "center", 112),
        (1, outer[1][0] + 10, outer[1][1] + 3, "left", 87),
        (2, outer[2][0] + 9, outer[2][1] - 4, "left", 94),
        (3, outer[3][0] - 9, outer[3][1] - 4, "right", 94),
        (4, outer[4][0] - 10, outer[4][1] + 3, "right", 87),
    ]

    c.setFillColor(INK)
    c.setFont("Arial-Bold", 8.5)
    for idx, x, y, align, width in label_specs:
        stage, question = STAGES[idx]
        draw_stage_label(c, stage, question if show_questions else "", x, y, align, width)

    if writing_lines:
        c.setStrokeColor(Color(INK.red, INK.green, INK.blue, alpha=0.15))
        c.setLineWidth(0.35)
        for i in range(5):
            angle = math.radians(90 - i * 72)
            for ratio in [0.3, 0.5, 0.7, 0.9]:
                x = cx + radius * ratio * math.cos(angle)
                y = cy + radius * ratio * math.sin(angle)
                c.circle(x, y, 2.1, stroke=1, fill=0)


def bullet(c, text, x, y, width, size=9, leading=12):
    c.setFillColor(BLUE)
    c.circle(x + 3, y + 3, 2.2, stroke=0, fill=1)
    return draw_wrapped(c, text, x + 13, y + 7, width - 13, "Arial", size, leading, INK_SOFT) - 4


def draw_field(c, label, x, y, width):
    c.setFillColor(MUTE)
    c.setFont("Courier-Bold", 6.5)
    c.drawString(x, y, label.upper())
    c.setStrokeColor(HAIRLINE)
    c.setLineWidth(0.5)
    c.line(x, y - 7, x + width, y - 7)


def page_one(c):
    draw_page_title(
        c,
        "FIELD TOOL  /  START WITH LISTENING",
        "The Whole Journey Communications Audit",
        "A printable exercise for seeing who your current communication serves, where trust is being built, and where the journey goes quiet.",
        1,
    )

    c.setFillColor(INK)
    c.setFont("Georgia-Bold", 14)
    c.drawString(MARGIN, 626, "What this map reveals")
    y = 603
    bullets = [
        "Each spoke represents one stage of the future congregation journey: Awareness, Visit, Attend, Member, and Minister.",
        "The center represents niche or insider communication that assumes significant context and trust.",
        "The outside represents broad or public communication that can be understood by more people with less context.",
        "Plot every current communication piece on the spoke for the stage it primarily serves. Place a piece between spokes when it genuinely serves both.",
        "Look for crowded areas, empty stages, and places where communication attracts attention but provides no handoff to the next faithful step.",
    ]
    for item in bullets:
        y = bullet(c, item, MARGIN, y, 250, 8.5, 11.2)

    draw_journey_map(c, 456, 478, 91, show_questions=False)

    c.setFillColor(PALE_BLUE)
    c.roundRect(MARGIN, 255, PAGE_W - MARGIN * 2, 90, 5, stroke=0, fill=1)
    c.setFillColor(INK)
    c.setFont("Georgia-Bold", 12)
    c.drawString(MARGIN + 18, 319, "The goal is not to move every dot to the outside.")
    draw_wrapped(
        c,
        "Broad communication is not automatically better, and insider communication is not automatically a problem. A healthy strategy intentionally serves people across all five stages and creates clear handoffs between them.",
        MARGIN + 18,
        299,
        PAGE_W - MARGIN * 2 - 36,
        "Georgia",
        9.2,
        12.5,
        INK_SOFT,
    )

    c.setFillColor(INK)
    c.setFont("Georgia-Bold", 13)
    c.drawString(MARGIN, 215, "Before you begin")
    y = 194
    for item in [
        "Gather a representative sample: videos, social posts, emails, web pages, signage, forms, classes, invitations, and follow-up communication.",
        "Work with people from communications, ministry, hospitality, groups, and volunteer leadership. The future congregation experiences one church, not separate departments.",
        "Use pencil or removable notes. The first map should describe the journey people experience now, not the one the team hopes it created.",
    ]:
        y = bullet(c, item, MARGIN, y, PAGE_W - MARGIN * 2, 8.7, 11.5)

    draw_field(c, "Church / Organization", MARGIN, 75, 230)
    draw_field(c, "Workshop Date", 330, 75, 120)
    draw_field(c, "Facilitator", 470, 75, 104)
    c.showPage()


def page_two(c):
    draw_page_title(
        c,
        "WORKSHEET  /  COMMUNICATIONS INVENTORY",
        "Name what you are already making",
        "List recurring communication and current campaigns before plotting. Do not judge the work yet. First, make the system visible.",
        2,
    )

    columns = [
        ("COMMUNICATION PIECE", 132),
        ("CHANNEL", 73),
        ("PRIMARY STAGE", 75),
        ("REACH 1-5", 55),
        ("NEXT FAITHFUL STEP", 123),
        ("KEEP / CHANGE / STOP", 78),
    ]
    table_x = MARGIN
    table_top = 625
    header_h = 32
    row_h = 29
    rows = 15
    total_w = sum(width for _, width in columns)

    c.setFillColor(INK)
    c.rect(table_x, table_top - header_h, total_w, header_h, stroke=0, fill=1)
    c.setFillColor(WHITE)
    c.setFont("Courier-Bold", 5.8)
    x = table_x
    for label, width in columns:
        for line_index, line in enumerate(wrap(label, "Courier-Bold", 5.8, width - 8)):
            c.drawString(x + 4, table_top - 13 - line_index * 7, line)
        x += width

    c.setStrokeColor(HAIRLINE)
    c.setLineWidth(0.4)
    x = table_x
    for _, width in columns:
        c.line(x, table_top, x, table_top - header_h - rows * row_h)
        x += width
    c.line(table_x + total_w, table_top, table_x + total_w, table_top - header_h - rows * row_h)

    y = table_top - header_h
    for row in range(rows + 1):
        c.line(table_x, y, table_x + total_w, y)
        y -= row_h

    c.setFillColor(MUTE)
    c.setFont("Courier", 6.2)
    c.drawString(MARGIN, 142, "REACH KEY")
    c.setFont("Arial", 7.5)
    c.drawString(MARGIN, 128, "1 = niche / insider      3 = some context required      5 = broad / public")

    c.setFont("Courier", 6.2)
    c.drawString(MARGIN, 102, "STAGE KEY")
    c.setFont("Arial", 7.5)
    c.drawString(MARGIN, 88, "Awareness  /  Visit  /  Attend  /  Member  /  Minister")

    c.setFillColor(RUST)
    c.setFont("Courier-Bold", 6.2)
    c.drawRightString(PAGE_W - MARGIN, 88, "ONE PIECE MAY SERVE TWO ADJACENT STAGES")
    c.showPage()


def page_three(c):
    draw_page_title(
        c,
        "WORKSHEET  /  CURRENT STATE",
        "Plot the journey you actually created",
        "Write an abbreviation for each communication piece on the spoke it serves. Move outward for broader reach and inward for more niche or insider communication.",
        3,
    )

    draw_journey_map(c, PAGE_W / 2, 395, 180, show_questions=True, writing_lines=True)

    c.setFillColor(INK)
    c.setFont("Georgia-Bold", 11)
    c.drawString(MARGIN, 137, "Read the shape before you solve it")
    prompts = [
        ("Where is the map crowded?", MARGIN),
        ("Where does the journey go quiet?", 217),
        ("Where is the handoff missing?", 396),
    ]
    for prompt, x in prompts:
        c.setFillColor(MUTE)
        c.setFont("Arial-Bold", 7.5)
        c.drawString(x, 118, prompt)
        c.setStrokeColor(HAIRLINE)
        c.setLineWidth(0.45)
        for line_y in [101, 84, 67, 50]:
            c.line(x, line_y, x + 160, line_y)
    c.showPage()


def page_four(c):
    draw_page_title(
        c,
        "WORKSHEET  /  FUTURE STATE",
        "Plan the next part of the road",
        "Plot the communication you need next. Build a balanced strategy around real gaps, clear handoffs, and the next faithful step at every stage.",
        4,
    )

    draw_journey_map(c, PAGE_W / 2, 465, 155, show_questions=False, writing_lines=True)

    c.setFillColor(INK)
    c.setFont("Georgia-Bold", 11.5)
    c.drawString(MARGIN, 260, "Five decisions for the next season")

    table_x = MARGIN
    table_top = 242
    header_h = 26
    row_h = 31
    columns = [
        ("STAGE", 66),
        ("THE GAP WE SEE", 142),
        ("ONE COMMUNICATION ACTION", 184),
        ("OWNER / DATE", 144),
    ]
    total_w = sum(w for _, w in columns)
    c.setFillColor(INK)
    c.rect(table_x, table_top - header_h, total_w, header_h, stroke=0, fill=1)
    c.setFillColor(WHITE)
    c.setFont("Courier-Bold", 5.9)
    x = table_x
    for label, width in columns:
        c.drawString(x + 4, table_top - 16, label)
        x += width

    c.setStrokeColor(HAIRLINE)
    c.setLineWidth(0.4)
    x = table_x
    for _, width in columns:
        c.line(x, table_top, x, table_top - header_h - row_h * 5)
        x += width
    c.line(table_x + total_w, table_top, table_x + total_w, table_top - header_h - row_h * 5)

    y = table_top - header_h
    c.setFont("Arial-Bold", 7.2)
    for idx, (stage, _) in enumerate(STAGES):
        c.setFillColor(PALE_BLUE if idx % 2 == 0 else WHITE)
        c.rect(table_x, y - row_h, total_w, row_h, stroke=0, fill=1)
        c.setStrokeColor(HAIRLINE)
        c.line(table_x, y, table_x + total_w, y)
        c.setFillColor(INK)
        c.drawString(table_x + 4, y - 19, stage.title())
        y -= row_h
    c.setStrokeColor(HAIRLINE)
    c.line(table_x, y, table_x + total_w, y)

    c.setFillColor(RUST)
    c.setFont("Courier-Bold", 6.7)
    c.drawString(MARGIN, 49, "THE TEST")
    c.setFillColor(INK)
    c.setFont("Georgia-Bold", 9.5)
    c.drawString(MARGIN + 54, 49, "Does every stage have a clear next step, and has someone prepared it?")
    c.showPage()


def build():
    register_fonts()
    os.makedirs(os.path.dirname(OUTPUT), exist_ok=True)
    os.makedirs(os.path.dirname(SITE_COPY), exist_ok=True)

    c = canvas.Canvas(OUTPUT, pagesize=letter, pageCompression=1)
    c.setTitle("The Whole Journey Communications Audit")
    c.setAuthor("Brent Young")
    c.setSubject("A printable church communications strategy exercise")
    c.setCreator("Brent Young")

    page_one(c)
    page_two(c)
    page_three(c)
    page_four(c)
    c.save()
    shutil.copyfile(OUTPUT, SITE_COPY)
    print(OUTPUT)
    print(SITE_COPY)


if __name__ == "__main__":
    build()
